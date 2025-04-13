<?php

namespace App\Http\Controllers\Api\Category;

use App\Models\Story;
use App\Models\Customers;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StoryResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ShowStoryResource;
use Illuminate\Support\Facades\Cache;

use App\Http\Resources\SubcategoryResource;
use App\Http\Resources\CategoryStoryResource;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function MainCategory()
    {
        // Define a unique cache key for the main categories
        $cacheKey = 'main_categories';
    
        // Retrieve the cached results or execute the query and cache the results for 24 hours
        $categories = Cache::remember($cacheKey, now()->addHours(24), function () {
            return Category::with('stories')->whereNull('parent_id')->get();
        });
    
        // Return the collection of categories using the CategoryResource
        return CategoryResource::collection($categories);
    }
    public function SubCategory($cat_id)
    {
        // Get the authenticated user
        $user = auth()->guard('customer')->user();
    
        // Define a unique cache key for the subcategories and stories based on the category ID and user ID
        $cacheKey = "subcategory_{$cat_id}_user_{$user->id}";
    
        // Retrieve the cached results or execute the query and cache the results for 24 hours
        $data = Cache::remember($cacheKey, now()->addHours(24), function () use ($cat_id, $user) {
            // Retrieve subcategories
            $categories = Category::where('parent_id', $cat_id)->get();
    
            // Retrieve stories with additional conditions
            $stories = Customers::whereHas('stories', function ($query) use ($cat_id) {
                $query->where('expires_at', '>', now())
                      ->where('cat_id', $cat_id);
            })
            ->withCount(['stories as stories_count' => function ($query) use ($cat_id) {
                $query->where('expires_at', '>', now())
                      ->where('cat_id', $cat_id);
            }])
            ->with(['stories' => function($query) use ($user, $cat_id) {
                $query->where('cat_id', $cat_id)
                      ->withCount(['views as views_count' => function($query) use ($user) {
                          $query->where('viewer_id', $user->id);
                      }]);
            }])
            ->get();
    
            // Return the data as an array
            return [
                'categories' => $categories,
                'stories' => $stories,
            ];
        });
    
        // Return the cached or freshly retrieved data using the appropriate resources
        return [
            'categories' => CategoryResource::collection($data['categories']),
            'stories' => StoryResource::collection($data['stories']),
        ];
    }

    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
