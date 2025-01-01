<?php

namespace App\Http\Controllers\Api;

use App\Models\NormalAds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{

    public function allFavorite()
    {
        $customer = Auth::guard('customer')->user();
    
        if (!$customer) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        // Get normal ads favorites
        $normalAds = DB::table('favorites')
            ->join('normal_ads', 'favorites.ad_id', '=', 'normal_ads.id')
            ->where('favorites.customer_id', $customer->id)
            ->where('favorites.ad_type', 'normal')
            ->select(
                'normal_ads.id',
                'normal_ads.title',
                'normal_ads.price',
                'normal_ads.photo',
                'normal_ads.created_at'
            )->get();
    
        // Get commercial ads favorites
        $commercialAds = DB::table('favorites')
            ->join('commercial_ads', 'favorites.ad_id', '=', 'commercial_ads.id')
            ->where('favorites.customer_id', $customer->id)
            ->where('favorites.ad_type', 'commercial')
            ->select(
                'commercial_ads.id',
                'commercial_ads.title',
                'commercial_ads.photo_path',
                'commercial_ads.created_at'
            )->get();
    
        $normalData = $normalAds->map(function ($favorite) {
            return [
                'id' => $favorite->id,
                'title' => $favorite->title,
                'price' => $favorite->price,
                'featured_photo' => asset('storage/' . $favorite->photo),
                'created_time' => \Carbon\Carbon::parse($favorite->created_at)->diffForHumans(),
            ];
        });
    
        $commercialData = $commercialAds->map(function ($favorite) {
            return [
                'id' => $favorite->id,
                'title' => $favorite->title,
                'featured_photo' => asset('storage/' . $favorite->photo_path),
                'created_time' => \Carbon\Carbon::parse($favorite->created_at)->diffForHumans(),
            ];
        });
    
        return response()->json([
            'normal_ads' => $normalData,
            'commercial_ads' => $commercialData,
            'normal_count' => $normalData->count(),
            'commercial_count' => $commercialData->count(),
            'total_favorites' => $normalData->count() + $commercialData->count()
        ]);
    }
    
    public function toggleFavorite(Request $request)
    {
        // Get the authenticated customer
        $customer = Auth::guard('customer')->user();
    
        if (!$customer) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        try {
            // Validate request
            $request->validate([
                'ad_id' => 'required|integer',
                'ad_type' => 'required|in:normal,commercial'
            ]);
    
            // Check if favorite exists
            $exists = DB::table('favorites')
                ->where('customer_id', $customer->id)
                ->where('ad_id', $request->ad_id)
                ->where('ad_type', $request->ad_type)
                ->exists();
    
            if ($exists) {
                // Remove favorite
                DB::table('favorites')
                    ->where('customer_id', $customer->id)
                    ->where('ad_id', $request->ad_id)
                    ->where('ad_type', $request->ad_type)
                    ->delete();
    
                $message = 'Product removed from favorites.';
                $status = false;
            } else {
                // Add favorite
                DB::table('favorites')->insert([
                    'customer_id' => $customer->id,
                    'ad_id' => $request->ad_id,
                    'ad_type' => $request->ad_type,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
    
                $message = 'Product added to favorites.';
                $status = true;
            }
    
            return response()->json([
                'message' => $message,
                'status' => $status,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email
                ]
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error processing favorite',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function removeFavorite(NormalAds $normal)
    {
        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($customer->hasFavorited($normal)) {
            $customer->favorites()->detach($normal->id);

            return response()->json([
                'message' => 'Product removed from favorites.',
                'status' => false,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                ]
            ]);
        }

        return response()->json([
            'message' => 'Product is not in favorites.',
            'status' => false,
        ]);
    }
}