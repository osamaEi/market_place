<?php

namespace App\Http\Controllers\Api\Search;

use App\Models\Filter;
use App\Models\Category;
use App\Models\NormalAds;
use App\Services\Exchange;
use Illuminate\Http\Request;
use Modules\House\Models\Feature;
use Modules\Car\Models\CarFeature;
use Modules\Car\Models\Brand;
use App\Http\Controllers\Controller;
use Modules\Bike\Models\BikeFeature;
use App\Http\Resources\PopupResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\BannerResource;
use Illuminate\Support\Facades\Session;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\NormalAdResource;
use App\Http\Resources\CommercialResource;

class FiltrationController extends Controller
{
    
    
    
    public function isFilter($cat_id) {

        $filter = Filter::where('cat_id', $cat_id)->get();
    
        if ($filter->isNotEmpty()) {
            return response()->json(['result' => true]);
        }
    
        return response()->json(['result' => false]);
    }
    
    
    public function showFilters($cat_id)
    {
        // Cache the filters based on the category ID for better performance
        $filters = Cache::remember('filters_for_cat_' . $cat_id, now()->addHours(24), function () use ($cat_id) {
            return Filter::where('cat_id', $cat_id)->get();
        });
    
        // Initialize arrays for features and brands
        $features = [];
        $brands = [];
    
        // Iterate over each filter and check its relation to determine features and brands
        foreach ($filters as $filter) {
            if ($filter->relation_name == 'cars.features') {
                $features = Cache::remember('car_features', now()->addHours(24), function () {
                    return CarFeature::all();
                });
                $brands = Cache::remember('brands', now()->addHours(24), function () {
                    return Brand::all();
                });
            } elseif ($filter->relation_name == 'houses.features') {
                $features = Cache::remember('house_features', now()->addHours(24), function () {
                    return Feature::all();
                });
            } elseif ($filter->relation_name == 'bikes.features') {
                $features = Cache::remember('bike_features', now()->addHours(24), function () {
                    return BikeFeature::all();
                });
            }
        }
    
        // Return the response with filters, features, and brands
        return response()->json([
            'filters' => $filters,
            'features' => $features,
            'brands' => $brands
        ]);
    }
    
    public function getRelatedAds($cat_id)
    {
        // Cache the related ads data for the given category
        $subCategories = Cache::remember('subCategories_for_' . $cat_id, now()->addHours(24), function () use ($cat_id) {
            return Category::with('normalAds')->where('parent_id', $cat_id)->get();
        });
    
        $Categories = Cache::remember('categories_for_' . $cat_id, now()->addHours(24), function () use ($cat_id) {
            return Category::with(['commercialAds', 'banners', 'popupAds'])
                           ->where('id', $cat_id)
                           ->get();
        });
    
        // Flatten collections of ads
        $normalAds = $subCategories->flatMap->normalAds;
        $commercialAds = $Categories->flatMap->commercialAds;
        $banners = $Categories->flatMap->banners;
        $popupAds = $Categories->flatMap->popupAds;
    
        // Return the response with the related ads and categories
        return response()->json([
            'MainCategories' => CategoryResource::collection($Categories),
            'subCategories' => CategoryResource::collection($subCategories),
            'normalAds' => NormalAdResource::collection($normalAds),
            'commercialAds' => CommercialResource::collection($commercialAds),
            'banners' => BannerResource::collection($banners),
            'popupAds' => PopupResource::collection($popupAds)
        ]);
    }
    


 public function applyFilters(Request $request, $cat_id)
{
    $query = NormalAds::Featured()->active()->where('cat_id', $cat_id);


    if ($request->has('title')) {
        $title = $request->input('title');
        $this->applyTextSelectNumberFilter($query, 'title', $title, null);
    }

    if($sortType = $request->input('sort')){
        $this->sort($query, $sortType);
    }





  
    $filters = Filter::where('cat_id', $cat_id)->get();

    foreach ($filters as $filter) {
        $filterName = $filter->filter_name;
        $filterType = $filter->filter_type;
        $relation = $filter->relation_name;

        $value = $request->input($filterName);

        if ($value || $filterType === 'min_max') {
            if ($filterType == 'text' || $filterType == 'select' || $filterType == 'number') {
                $this->applyTextSelectNumberFilter($query, $filterName, $value, $relation);
            } elseif ($filterType == 'checkbox' && $filterName == 'features') {
                $this->applyCheckboxFilter($query, $relation, $request->input('features', []));
            } elseif ($filterType == 'min_max') {
                $this->applyMinMaxFilter($query, $request, $filterName, $relation);
            }
        }
    }

    // Get the filtered results
    $normalAds = $query->get();

    // Return the results as a collection of NormalAdResource
    return NormalAdResource::collection($normalAds);
}

private function sort(&$query, $sortType)
{
    switch ($sortType) {
        case 'latest':
            $query->orderBy('created_at', 'desc'); 
            break;
        case 'oldest':
            $query->orderBy('created_at', 'asc'); 
            break;
        case 'high_price':
            $query->orderBy('price', 'desc'); 
            break;
        case 'low_price':
            $query->orderBy('price', 'asc'); 
            break;
        default:
            $query->orderBy('created_at', 'desc'); 
            break;
    }
}



    
    private function applyTextSelectNumberFilter(&$query, $filterName, $value, $relation)
    {
        if ($relation) {
            $query->whereHas($relation, function ($q) use ($filterName, $value) {
                $q->where($filterName, 'like', '%' . $value . '%');
            });
        } else {
            $query->where($filterName, 'like', '%' . $value . '%');
        }
    }
    
    private function applyCheckboxFilter(&$query, $relation, $selectedFeatures)
    {
        if (!empty($selectedFeatures)) {
            $query->whereHas($relation, function ($q) use ($selectedFeatures) {
                $q->whereIn('feature_id', $selectedFeatures);
            });
        }
    }
    
    private function applyMinMaxFilter(&$query, $request, $filterName, $relation)
    {
        $minInput = $request->input('min_' . $filterName);
        $maxInput = $request->input('max_' . $filterName);
    
        if ($minInput) {
            if ($relation) {
                $query->whereHas($relation, function ($q) use ($filterName, $minInput) {
                    $q->where($filterName, '>=', $minInput);
                });
            } else {
                $query->where($filterName, '>=', $minInput);
            }
        }
    
        if ($maxInput) {
            if ($relation) {
                $query->whereHas($relation, function ($q) use ($filterName, $maxInput) {
                    $q->where($filterName, '<=', $maxInput);
                });
            } else {
                $query->where($filterName, '<=', $maxInput);
            }
        }
    }
    


}
