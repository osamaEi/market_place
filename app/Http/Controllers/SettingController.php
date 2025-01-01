<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Setting;
use App\Models\complain;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request) {
        $settings = Setting::where('key', 'comment')->get();
    
        // Get filter inputs
        $title = $request->get('title');
        $customer = $request->get('customer');
        $status = $request->get('status');
    
        // Build the query with filters
        $commentsQuery = Comment::with(['commentable', 'customer']);
        
        if ($title) {
            $commentsQuery->whereHas('commentable', function ($query) use ($title) {
                $query->where('title', 'like', '%' . $title . '%');
            });
        }
    
        if ($customer) {
            $commentsQuery->whereHas('customer', function ($query) use ($customer) {
                $query->where('name', 'like', '%' . $customer . '%');
            });
        }
    
        if ($status !== null) {
            $commentsQuery->where('status', $status);
        }
    
        $comments = $commentsQuery->get();
    
        return view('backend.settings.index', compact('settings', 'comments'));
    }
    


    public function complain() {

        $settings  = Setting::where('key','complain')->first();

        $complains = complain::with(['complainable', 'customer'])->get();

        return view('backend.complains.index',compact('settings','complains'));

    } 

    public function update(Setting $setting) {

        $setting->value = ! $setting->value;

        $setting->update();

        return redirect()->back()->with('success', __('Data saved successfully'));

    }


    
    public function comment_toggle(Comment $comment) {

        $comment->status = ! $comment->status;

        $comment->update();

        return redirect()->back()->with('success', __('comment activated successfully.'));

    } 

    public function destroyComment(Comment $comment) {
        $comment->delete();
        return redirect()->back()->with('success', __('Comment deleted successfully.'));
    } 

    
    public function destroyComplain(Complain $complain) {
        $complain->delete();
        return redirect()->back()->with('success', __('Comment deleted successfully.'));
    }
} 
 