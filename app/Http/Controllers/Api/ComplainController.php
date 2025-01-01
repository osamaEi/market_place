<?php

namespace App\Http\Controllers\Api;

use App\Models\complain;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ComplainController extends Controller
{
   
        public function store(Request $request)
        {
            // Corrected validation rule
            $request->validate([
                'text' => 'required|string',
                'complainable_id' => 'required|integer',
                'complainable_type' => 'required|string|in:App\Models\CommercialAd,App\Models\NormalAds' // Changed from comable_type to complainable_type
            ]);
    
            try {
                // Ensure the complainable type is correct
                $complainable = $request->complainable_type::findOrFail($request->complainable_id);
    
                $complain = new complain(); // Use the correct casing for the model
                $complain->text = $request->text;
                $complain->customer_id = Auth::guard('customer')->id();
                $complain->complainable_type = $request->complainable_type;
                $complain->complainable_id = $request->complainable_id;
                $complain->save();
    
                return response()->json([
                    'status' => 'success',
                    'message' => 'Complaint added successfully',
                    'data' => $complain->load(['customer', 'complainable'])
                ]);
    
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to add complaint',
                    'error' => $e->getMessage()
                ], 500);
            }
        } 
    }
    

