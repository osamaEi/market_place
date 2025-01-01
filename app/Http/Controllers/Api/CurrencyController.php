<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Currency;
use App\Models\Customers;
use App\Services\Exchange;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Resources\CurrencyResource;

class CurrencyController extends Controller
{

    public function index(){

        $currencies = Currency::all();

        return CurrencyResource::collection($currencies);

    }


    public function selectedCustomerCurrency(){

        $customer= Auth::guard('customer')->user();

        if($customer->currency){
        $selected = $customer->currency->code;

        return response()->json([
            'currency_selected' =>$selected,
        ]);
        }else{
            return response()->json([
                'currency_selected' =>'not selected yet',
            ]);
            

}
        
    }






    public function store(Request $request)
    {
        $request->validate([
            'currency_id' => 'required|exists:currencies,id', // Ensure the currency exists
        ]);
    
        $customer = Auth::guard('customer')->user(); // Get the authenticated customer instance
        if (!$customer) {
            return response()->json(['error' => 'Customer not authenticated'], 401); // Handle unauthenticated case
        }
    
        $currencyId = $request->input('currency_id');
    
        $customer->currency_id = $currencyId;
        
        $customer->save();

    
        return response()->json([
            'message' => 'Currency updated successfully',
        ], 200);
    }
    
    
}


    
    
