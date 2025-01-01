<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use App\Models\Setting;
use App\Models\NormalAds;
use App\Models\CommercialAd;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
   



    public function activate_comment() {


        $settings = Setting::where('key','comment')->first();

        $activted = $settings->value;

        return response()->json([
            'activated_comments' => $activted, 
        ]);
    }



        public function store(Request $request)
        {

            $request->validate([
                'text' => 'required|string',
                'commentable_id' => 'required|integer',
                'commentable_type' => 'required|string|in:App\Models\CommercialAd,App\Models\NormalAds'
            ]);
    
            try {
                $commentable = $request->commentable_type::findOrFail($request->commentable_id);
    
                $comment = new Comment();
                $comment->text = $request->text;
                $comment->customer_id = Auth::guard('customer')->id();
                $comment->commentable_type = $request->commentable_type;
                $comment->commentable_id = $request->commentable_id;
                $comment->save();
    
                return response()->json([
                    'status' => 'success',
                    'message' => 'Comment added successfully',
                    'data' => $comment->load(['customer', 'commentable'])
                ]);
    
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to add comment',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

    

    }


