<?php

namespace App\Http\Controllers\Api;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::all();
    
        $formattedCountries = $countries->map(function ($country) {
            return [
                'id' => $country->id,
                'name' => __($country->name), 
                'code' => $country->code, 
            ];
        });
    
        return response()->json([
            'countries' => $formattedCountries,
        ]);
    }



    public function selectedCountry(){


            $customer= Auth::guard('customer')->user();
    
            if($customer->country){
            $selected = $customer->country;
    
            return response()->json([
                'country_selected' =>$selected,
            ]);
            }else{
                return response()->json([
                    'country_selected' =>'not selected yet',
                ]);
                
    
    }

    
    }

    public function store(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id', // Ensure the currency exists
        ]);
    
        $customer = Auth::guard('customer')->user(); // Get the authenticated customer instance
        if (!$customer) {
            return response()->json(['error' => 'Customer not authenticated'], 401); // Handle unauthenticated case
        }
    
        $country_id = $request->input('country_id');
    
        $customer->country_id = $country_id;
        
        $customer->save();

    
        return response()->json([
            'message' => 'country updated successfully',
        ], 200);
    }
    
}

