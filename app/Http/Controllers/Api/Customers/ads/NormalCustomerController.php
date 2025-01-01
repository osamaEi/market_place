<?php

namespace App\Http\Controllers\Api\Customers\ads;

use App\Models\NormalAds;
use App\Models\CommercialAd;
use Illuminate\Http\Request;
use Modules\Car\Models\Cars;
use Modules\Bike\Models\Bike;
use Modules\House\Models\House;
use Modules\Career\Models\Careers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Modules\Electronics\Models\Mobiles;
use App\Http\Resources\NormalAdResource;
use App\Http\Resources\CommercialResource;

class NormalCustomerController extends Controller
{


    public function index()
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json([
                'message' => __('Customer not authenticated.')
            ], 401);
        }
    
        $customerId = $customer->id;
    
        // Get Normal Ads with comment count
        $normalAds = NormalAds::where('customer_id', $customerId)
        ->Featured()
            ->withCount(['comments' => function($query) {
                $query->where('status', 1);
            }])
            ->get();
        
        $normalAdResources = NormalAdResource::collection($normalAds);
        $normalAdsCount = $normalAds->count();
        $normalCommentsCount = $normalAds->sum('comments_count');
    
        // Get Commercial Ads with comment count
        $commercial = CommercialAd::where('customer_id', $customerId)
        ->Featured()
            ->withCount(['comments' => function($query) {
                $query->where('status', 1);
            }])
            ->get();
        
        $commercialAdResources = CommercialResource::collection($commercial);
        $commercialCount = $commercial->count();
        $commercialCommentsCount = $commercial->sum('comments_count');
    
        // Calculate totals
        $totalAdsCount = $normalAdsCount + $commercialCount;
        $totalCommentsCount = $normalCommentsCount + $commercialCommentsCount;
    
        return response()->json([
            'normal_ads' => [
                'count' => $normalAdsCount,
                'comments_count' => $normalCommentsCount,
                'data' => $normalAdResources,
            ],
            'commercials' => [
                'count' => $commercialCount,
                'comments_count' => $commercialCommentsCount,
                'data' => $commercialAdResources,
            ],
            'total_count' => $totalAdsCount,
            'total_comments_count' => $totalCommentsCount,
        ], 200);
    }
    public function getTotalCommentsAndFavorites()
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json([
                'message' => 'Customer not authenticated.'
            ], 401);
        }
    
        $customerId = $customer->id;
    
        // Get total comments count for Normal Ads
        $normalAdsCommentsCount = NormalAds::where('customer_id', $customerId)
            ->withCount(['comments' => function ($query) {
                $query->where('status', 1);
            }])
            ->get()
            ->sum('comments_count');
    
        // Get total comments count for Commercial Ads
        $commercialAdsCommentsCount = CommercialAd::where('customer_id', $customerId)
            ->withCount(['comments' => function ($query) {
                $query->where('status', 1);
            }])
            ->get()
            ->sum('comments_count');
            $favoritesCount  = DB::table('favorites')->where('customer_id',$customerId)->count();
     
    
        // Calculate total comments
        $totalCommentsCount = $normalAdsCommentsCount + $commercialAdsCommentsCount;
    
        return response()->json([
            'total_comments_count' => $totalCommentsCount,
           'total_favorites_count' => $favoritesCount,
        ], 200);
    }
    


    
    /**
     * Store a newly created resource in storage.
     */
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
