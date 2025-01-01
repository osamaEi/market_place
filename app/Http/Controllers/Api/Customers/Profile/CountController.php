<?php

namespace App\Http\Controllers\Api\Customers\Profile;

use App\Models\NormalAds;
use App\Models\CommercialAd;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CountController extends Controller
{
    
    public function countViewAds()
    {
        try {
            $customer = Auth::guard('customer')->user();
            if (!$customer) {
                return response()->json(['error' => __('Unauthenticated')], 401);
            }
    
            $customerId = $customer->id;
    
            $normalAdsViews = NormalAds::where('customer_id', $customerId)
                ->sum('views_count');
    
            $commercialAdsViews = CommercialAd::where('customer_id', $customerId)
                ->sum('views_count');
    
            $totalViews = $normalAdsViews + $commercialAdsViews;
    
            return response()->json([
                'totalViews' => $totalViews,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => __('An error occurred: ') . $e->getMessage()], 500);
        }
    }


    public function countAds(){

        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            return response()->json([
                'message' => 'Customer not authenticated.'
            ], 401);
        }
    
        $customerId = $customer->id;
    
        $normalAds = NormalAds::where('customer_id', $customerId)->get();
        $normalAdsCount = $normalAds->count();

        $commercial = CommercialAd::where('customer_id', $customerId)->get();
        $commercialCount = $commercial->count();
    
        $totalAdsCount = $normalAdsCount + $commercialCount;
    
        return response()->json([
            
            'total_count' => $totalAdsCount, 
     ]);




    }
        
    }

