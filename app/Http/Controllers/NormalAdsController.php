<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customers;
use App\Models\NormalAds;
use App\Exports\AdsExport;
use Illuminate\Http\Request;
use App\Models\ImageNormalAds;
use App\Services\AdLimitServices;
use App\Services\FirebaseService;
use App\Services\NormalAdsService;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;

class NormalAdsController extends Controller
{
    protected $normalAdsService;
    protected $firebaseService;

    public function __construct(NormalAdsService $normalAdsService, FirebaseService $firebaseService)
    {
        $this->normalAdsService = $normalAdsService;
        $this->firebaseService = $firebaseService;
    }

    public function export()
    {
        return Excel::download(new AdsExport, 'normal.xlsx');
    }
    public function index(Request $request)
    {
        // Get date ranges
        $now = now();
        $weekAgo = $now->copy()->subWeek();
        $dayAgo = $now->copy()->subDay();
    
        // Calculate weekly data
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $weeklyData[$date->format('D')] = NormalAds::whereDate('created_at', $date)->count();
        }
    
        // Initialize query
        $query = NormalAds::query();
    
        // Calculate statistics
        $statistics = [
            'total_ads' => NormalAds::count(),
            'active_ads' => NormalAds::where('is_active', true)->count(),
            'ads_this_week' => NormalAds::whereBetween('created_at', [$weekAgo, $now])->count(),
            'ads_today' => NormalAds::whereBetween('created_at', [$dayAgo, $now])->count(),
            'total_value' => NormalAds::sum('price'),
            'categories_count' => Category::count(),
            'popular_categories' => Category::withCount('normalAds')
                ->orderBy('normal_ads_count', 'desc')
                ->take(5)
                ->get(),
            'recent_activity' => NormalAds::with(['category', 'customer'])
                ->latest()
                ->take(5)
                ->get(),
            'weekly_data' => $weeklyData,
        ];
    
        // Calculate trends
        $previousWeek = NormalAds::whereBetween('created_at', 
            [$weekAgo->copy()->subWeek(), $weekAgo])->count();
        $statistics['weekly_growth'] = $previousWeek != 0 
            ? (($statistics['ads_this_week'] - $previousWeek) / $previousWeek) * 100 
            : 100;
    
        // Monthly trend data
        $monthlyData = collect(range(0, 11))->map(function($i) {
            $date = now()->subMonths($i);
            return [
                'month' => $date->format('M'),
                'count' => NormalAds::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count()
            ];
        })->reverse()->values();
    
        $statistics['monthly_data'] = $monthlyData;
    
        // Apply filters
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->input('search') . '%');
        }
    
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }
        
        if ($request->filled('is_featured')) {
            $query->where('is_featured',$request->input('is_featured'));
        }
    
    
        if ($request->filled('category_id')) {
            $query->where('cat_id', $request->input('category_id'));
        }
    
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }
    
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }
    
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }
    
        // Handle export
        if ($request->has('export')) {
            return Excel::download(
                new AdsExport($query->with('category', 'customer')->get()), 
                'ads.xlsx'
            );
        }
    
        // Get paginated results
        $ads = $query
        ->withoutGlobalScope('country')
            ->latest()
            ->paginate(10);
    
        $categories = Category::all();
        $customers = Customers::all();
    
        // Calculate category distribution
        $categoryDistribution = Category::withCount('normalAds')
            ->having('normal_ads_count', '>', 0)
            ->get()
            ->map(function($category) use ($statistics) {
                return [
                    'category' => $category->title,
                    'count' => $category->normal_ads_count,
                    'percentage' => ($category->normal_ads_count / $statistics['total_ads']) * 100
                ];
            });
    
        return view('backend.normalads.index', compact(
            'ads', 
            'categories', 
            'customers', 
            'statistics',
            'weeklyData',
            'monthlyData',
            'categoryDistribution'
        ));
    }

    public function selectCategory()
    {
        $categories = Category::whereNull('parent_id')->get();

        return view('backend.normalads.category', compact('categories'));
    }

    public function create(Request $request)
    {
        $category = Category::find($request->cat_id);

        if (!$category) {
            return redirect()->back()->with('error', 'Invalid category selected.');
        }

        $cat_id = $category->id;


        if ($category->id === 1) {

            return redirect()->route('car.create', ['cat_id' => $cat_id]);

        } elseif ($category->id === 2) {

            return redirect()->route('house.create', ['cat_id' => $cat_id]);
        }
         elseif ($category->id === 9) {

            return redirect()->route('bike.create', ['cat_id' => $cat_id]);

        }    elseif ($category->title === 'Careers') {

            return redirect()->route('career.create', ['cat_id' => $cat_id]);

        }
        elseif ($category->title === 'Mobiles') {

            return redirect()->route('mobile-normalAds.create', ['cat_id' => $cat_id]);

        }

        else{

            return view('backend.normalads.create',['cat_id' => $cat_id]);
        }

        return redirect()->back()->with('error', __('Form not available for this category.'));




    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'address' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'cat_id' => 'required|exists:categories,id',
            'customer_id' => 'required|exists:customers,id',
            'photo' => 'required|image|mimes:jpeg,png,jpg',
            'images.*' => 'image|mimes:jpeg,png,jpg',
            'is_active' => 'required|boolean'
        ]);
    
        try {
            DB::beginTransaction();
    
            // Handle main photo upload
            $photoPath = $request->file('photo')->store('ads/photos', 'public');
    
            // Create ad record
            $ad = NormalAds::create([
                'title' => $request->title,
                'address' => $request->address,
                'description' => $request->description,
                'price' => $request->price,
                'cat_id' => $request->cat_id,
                'customer_id' => $request->customer_id,
                'photo' => $photoPath,
                'is_active' => $request->is_active
            ]);
    
            // Handle multiple images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('ads/gallery', 'public');
                    AdImage::create([
                        'ad_id' => $ad->id,
                        'image' => $path
                    ]);
                }
            }
    
            DB::commit();
            return redirect()->route('normalads.index')->with('success', __('Ad created successfully'));
    
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', __('Error creating ad'))->withInput();
        }
    }


    protected function processAd(Request $request)
    {

        $normalAd = $this->normalAdsService->storeNormalAd($request);

    }

    public function update(Request $request, $id)
    {
        $model = NormalAds::findOrFail($id);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'cat_id' => 'required|integer',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'is_active' => 'required|boolean',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle file uploads for the main photo field
        if ($request->hasFile('photo')) {
            // Delete the old photo if it exists
            if ($model->photo) {
                Storage::disk('public')->delete($model->photo);
            }
            // Store the new photo
            $validatedData['photo'] = $request->file('photo')->store('photos', 'public');
        }

        // Update the model instance with the validated data
        $model->update($validatedData);

        // Handle image uploads for additional images
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            foreach ($images as $image) {
                $imagePath = $image->store('normal_ads_images', 'public');

                // Save each image record to the database
                ImageNormalAds::create([
                    'normal_ads_id' => $model->id,
                    'image_path' => $imagePath,
                ]);
            }
        }

        // Handle any additional operations like translations
        $this->translateAndSave($request->all(), 'update');

        return redirect()->route('normalads.index')->with('success', __('Record updated successfully.'));
    }
    public function show($id)
    {
        // Retrieve the NormalAds record by its ID
        $normalAd = NormalAds::with('images')->withoutGlobalScope('country')->findOrFail($id);

        // Pass the record and its related images to the view
        return view('backend.normalads.show', compact('normalAd'));
    }



public function toggleStatus(NormalAds $ad)

{
    $ad->is_active = !$ad->is_active; 

    $ad->save();

    try {
        $this->firebaseService->sendNotification(
            $ad->customer->fcm_token,
            'congratulatins',
            'Ad'.$ad->title.' has been activated',
            $request->data ?? []
        );

        return redirect()->back()->with('success', __('Ad status updated successfully!'));

    } catch (\Exception $e) {


        return redirect()->back()->with('warning', __('Ad status updated successfully! but notifaction not sent '.$e));

     
    }


}
public function destroy($id)
{
    $normalads = NormalAds::find($id);

    $normalads->delete();
 
    return redirect()->back(); 
}

}
