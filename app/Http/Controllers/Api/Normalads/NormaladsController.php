<?php

namespace App\Http\Controllers\Api\Normalads;

use App\Models\NormalAds;
use App\Jobs\TranslateText;
use Illuminate\Http\Request;
use Modules\Car\Models\Cars;
use Modules\Bike\Models\Bike;
use App\Models\ImageNormalAds;
use Modules\House\Models\House;
use App\Services\AdLimitServices;
use App\Services\NormalAdsService;
use Modules\Career\Models\Careers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\Electronics\Models\Mobiles;
use App\Http\Resources\NormalAdResource;
use App\Http\Resources\ShowNormalResource;

class NormaladsController extends Controller
{
    protected $normalAdsService;

    public function __construct(NormalAdsService $normalAdsService)
    {
        $this->normalAdsService = $normalAdsService;
    }

    public function index(Request $request)
    {
        // Generate a unique cache key based on the request parameters
        $cacheKey = 'normal_ads_' . md5(serialize($request->all()));
    
        // Attempt to retrieve the cached results
        $normalAds = Cache::remember($cacheKey, now()->addHours(24), function () use ($request) {
            // Create a query for retrieving active NormalAds
            $query = NormalAds::Active()->Featured();
    
            // Get the sorting parameter from the request
            $sortType = $request->input('sort'); // Example values: 'latest', 'oldest', 'high_price', 'low_price'
    
            // Apply sorting based on the provided sort type
            switch ($sortType) {
                case 'latest':
                    $query->orderBy('created_at', 'desc'); // Sort by latest
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc'); // Sort by oldest
                    break;
                case 'high_price':
                    $query->orderBy('price', 'desc'); // Sort by highest price
                    break;
                case 'low_price':
                    $query->orderBy('price', 'asc'); // Sort by lowest price
                    break;
                default:
                    // Default sorting (if no sort type is provided)
                    $query->orderBy('created_at', 'desc'); // Default to latest
                    break;
            }
    
            // Retrieve the sorted results
            return $query->get();
        });
    
        // Return the collection of NormalAds using the NormalAdResource
        return NormalAdResource::collection($normalAds);
    }

    
    /**
     * Store a newly created resource in storage.
     */


     public function store(Request $request)
     {
         
         $this->processAd($request);
    
            return response()->json(['message' => 'Record created successfully.']);
        }
    
    
    protected function processAd(Request $request)
    {
       
        $normalAd = $this->normalAdsService->storeNormalAd($request);

    }
    

   
    public function show($id)
    {
        // Use a dynamic cache key to store the ad by its ID
        $cacheKey = 'normalAd_' . $id;
    
        $normalAd = Cache::remember($cacheKey, now()->addHours(24), function () use ($id) {
            // Retrieve the ad and its related data
            $normalAd = NormalAds::findOrFail($id);
            $customer = Auth::guard('customer')->user();
    
            // If owner, show the ad with all relationships regardless of status
            if ($customer && $normalAd->customer_id === $customer->id) {
                $normalAd->load('category', 'images', 'cars', 'bikes', 'houses', 'mobiles');
                return $normalAd; // Return the ad if it’s the owner
            }
    
            // For non-owners, get active ad with relationships
            $activeNormalAd = NormalAds::with('category', 'images', 'cars', 'bikes', 'houses', 'mobiles')
                ->where('id', $id)
                ->active()
                ->first();
    
            if (!$activeNormalAd) {
                return response()->json([
                    'message' => 'Advertisement not found or not available'
                ], 404);
            }
    
            // Increment views for the original ad instance
            $activeNormalAd->increment('views_count');
    
            return $activeNormalAd; // Return the active ad for non-owners
        });
    
        // Return the ad as a resource
        return new ShowNormalResource($normalAd);
    }
    
     
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'cat_id' => 'required|integer',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'address' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        // Find the existing ad by its ID
        $normalAd = NormalAds::findOrFail($id);
    
        // Handle the main photo if a new one is uploaded
        if ($request->hasFile('photo')) {
            // Delete the old photo if it exists
            if ($normalAd->photo) {
                Storage::disk('public')->delete($normalAd->photo);
            }
    
            // Store the new photo
            $validatedData['photo'] = $request->file('photo')->store('photos', 'public');
        }
    
        // Update the ad fields
        $customer = Auth::guard('customer')->user();
        $countryId = $customer ? $customer->country_id : $request->session()->get('country_id');
    
        $normalAd->update([
            'title' => $validatedData['title'],
            'cat_id' => $validatedData['cat_id'],
            'description' => $validatedData['description'],
            'price' => $validatedData['price'],
            'address' => $validatedData['address'],
            'photo' => $validatedData['photo'] ?? $normalAd->photo, // Keep existing photo if not updated
            'country_id' => $countryId,
            'customer_id' => $customer->id,
        ]);
    
        // Handle additional images if new ones are uploaded
        if ($request->hasFile('images')) {
            // Delete the old images if they exist
            foreach ($normalAd->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }
    
            // Store the new images
            $images = $request->file('images');
            foreach ($images as $image) {
                $imagePath = $image->store('normal_ads_images', 'public');
    
                ImageNormalAds::create([
                    'normal_ads_id' => $normalAd->id,
                    'image_path' => $imagePath,
                ]);
            }
        }
    
        $this->translateAndSave($request->all(), 'update');
    
        return response()->json(['message' => 'Ad updated successfully.']);
    
    }
    
        // Translate and save the updated data
 
    
    
    
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
