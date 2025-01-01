<?php

namespace App\Http\Controllers\Api\Normalads;

use App\Models\Category;
use App\Models\NormalAds;
use App\Jobs\TranslateText;
use Illuminate\Http\Request;
use Modules\Bike\Models\Bike;
use App\Services\AdLimitServices;
use Modules\Bike\Models\BikeImages;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Bike\Models\BikeFeature;
use App\Http\Resources\BikeAdResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\NormalAdResource;
use Modules\Bike\Models\BikeSpecification;

class BikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     
     
public function index(Request $request)
{
    // Find the main category with id 9
    $mainCategory = Category::find(9);

    // If the main category is not found, return a 404 error response
    if (!$mainCategory) {
        return response()->json(['message' => 'Category not found'], 404);
    }

    // Get the IDs of all subcategories of the main category
    $subCategories = Category::where('parent_id', $mainCategory->id)->pluck('id');

    // Include the main category ID in the list of category IDs
    $categoryIds = $subCategories->prepend($mainCategory->id);

    // Create a query builder for NormalAds that belong to either the main category or its subcategories
    $normalAdsQuery = NormalAds::Active()  // Assuming you have a scope 'Active' for active ads
        ->whereIn('cat_id', $categoryIds);

    // Get the sorting parameter from the request
    $sortType = $request->input('sort'); // Example values: 'latest', 'oldest', 'high_price', 'low_price'

    // Apply sorting based on the provided sort type
    switch ($sortType) {
        case 'latest':
            $normalAdsQuery->orderBy('created_at', 'desc');
            break;
        case 'oldest':
            $normalAdsQuery->orderBy('created_at', 'asc');
            break;
        case 'high_price':
            $normalAdsQuery->orderBy('price', 'desc');
            break;
        case 'low_price':
            $normalAdsQuery->orderBy('price', 'asc');
            break;
        default:
            // Default sorting (if no sort type is provided)
            $normalAdsQuery->orderBy('created_at', 'desc');
            break;
    }

    // Execute the query and get the results
    $normalAds = $normalAdsQuery->get();

    // Return the ads using a resource collection
    return NormalAdResource::collection($normalAds);
}


public function bikeFeatures(){

    $bikes = BikeFeature::all();

    return response()->json([
        'bike_features' => $bikes
    ]);
}




    public function store(Request $request)
    {

      
        $adLimitService = new AdLimitServices();

    

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'cat_id' => 'required|exists:categories,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'features.*' => 'nullable|exists:bike_features,id',
        ]);
    

        $customer = Auth::guard('customer')->user();
        $countryId = $customer->country_id;
        $customerId = $customer->id;

        $ad = new NormalAds([
            'title' => $validatedData['title'],
            'country_id' => $countryId,
            'customer_id' => $customerId,
            'cat_id' => $validatedData['cat_id'],
            'address' => $validatedData['address'],
            'description' => $validatedData['description'],
            'price' => $validatedData['price'],
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_active' => false, 
        ]);

        
        if (!$adLimitService->canPostAd('normal')) {

            $ad->is_featured = null;

        } else {

            $ad->is_featured = true;
            
            $adLimitService->updateAdLimits('normal');

        }

 
        $ad->save();
    
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
            $ad->photo = $photoPath;
            $ad->save(); // Save the photo path to the existing ad record
        }
    
        // Handle additional images if uploaded
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('images', 'public');
                $ad->images()->create([
                    'image_path' => $imagePath,
                ]);
            }
        }
    
        $bike = new Bike([
            'color' => $request->input('color'),
            'year' => $request->input('year'),
            'kilo_meters' => $request->input('kilo_meters'),
            'normal_id' => $ad->id,
        ]);
        $bike->save();
    
        if ($request->has('features')) {
            $bike->features()->sync($request->input('features'));
        }
    
        $this->translateAndSave($request->all(), 'store');

        return response()->json(['success' => 'Bike created successfully.'], 201);

    }
    
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $bike = Bike::with('category')->findOrFail($id);
    
        return new BikeAdResource($bike);
    }


    /**
     * Update the specified resource in storage.
     */
   
        public function update(Request $request, $id)
        {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'cat_id' => 'required|exists:categories,id',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'features.*' => 'nullable|exists:bike_features,id',
            ]);
        
            // Find the ad by its ID
            $ad = NormalAds::findOrFail($id);
        
            // Update the ad fields
            $ad->update([
                'title' => $validatedData['title'],
                'country_id' => Auth::guard('customer')->user()->country,
                'cat_id' => $validatedData['cat_id'],
                'address' => $validatedData['address'],
                'description' => $validatedData['description'],
                'price' => $validatedData['price'],
                'is_active' => false, // Keep the current active status
            ]);
        
            // Update the photo if a new one is uploaded
            if ($request->hasFile('photo')) {
                // Delete the old photo if exists
                if ($ad->photo) {
                    Storage::disk('public')->delete($ad->photo);
                }
        
                // Store the new photo
                $photoPath = $request->file('photo')->store('photos', 'public');
                $ad->update(['photo' => $photoPath]);
            }
        
            // Handle additional images if uploaded
            if ($request->hasFile('images')) {
                // Optionally, delete old images if you want to replace them entirely
                // $ad->images()->delete();
        
                // Save new images
                foreach ($request->file('images') as $image) {
                    $imagePath = $image->store('images', 'public');
                    $ad->images()->create([
                        'image_path' => $imagePath,
                    ]);
                }
            }
        
            // Find the associated bike record and update it
            $bike = $ad->bikes;
        
            if ($bike) {
                $bike->update([
                    'color' => $request->input('color'),
                    'year' => $request->input('year'),
                    'kilo_meters' => $request->input('kilo_meters'),
                ]);
        
                // Sync the features
                if ($request->has('features')) {
                    $bike->features()->sync($request->input('features'));
                }
            } else {
                // Handle the case if there's no bike associated yet
                $bike = new Bike([
                    'color' => $request->input('color'),
                    'year' => $request->input('year'),
                    'kilo_meters' => $request->input('kilo_meters'),
                    'normal_id' => $ad->id,
                ]);
                $bike->save();
        
                if ($request->has('features')) {
                    $bike->features()->sync($request->input('features'));
                }
            }
        
            // Optionally, handle translation updates
            $this->translateAndSave($request->all(), 'update');

            return response()->json(['success' => 'Bike updated successfully.'], 200);

        }
        
    


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    protected function translateAndSave(array $inputs, $operation)
{
    $languages = ['en', 'fr', 'es', 'ar', 'de', 'tr', 'it', 'ja', 'zh', 'ur'];

    foreach ($inputs as $key => $value) {
        if (is_string($value) && !empty($value)) {
            dispatch(new TranslateText($key, $value, $languages));
        }
    }
}
}

