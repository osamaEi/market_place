<?php

namespace App\Http\Controllers\Api;

use App\Models\Story;
use App\Models\Customers;
use App\Models\StoryView; 
use App\Events\StoryEvent;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\StoryResource;
use Illuminate\Support\Facades\Storage; 
use App\Http\Resources\ShowStoryResource;
 

class StoryController extends Controller
{
 public function index()
    { 
        $user = auth()->guard('customer')->user();

        $UsersStories = Customers::whereHas('stories', function ($query) {
        $query->where('expires_at', '>', now());
       }) 
 
       ->withCount(['stories as stories_count' => function ($query) {
        $query->where('expires_at', '>', now());
      }])

       ->with(['stories' =>function($query) use ($user){
       $query->withCount(['views as views_count' =>function($query) use ($user){
       $query->where('viewer_id',$user->id);

        }]);
 
 
      }])
    
      ->get();
      //broadcast(new StoryEvent($story, 'viewed', Auth::id()))->toOthers();
        return StoryResource::collection($UsersStories);
    }

    public function show($id)
    {
        $story = Story::findOrFail($id); 
    
        return new ShowStoryResource($story);
    }

   
    
    
    public function store(Request $request)
    {
        $request->validate([
            'media_url' => 'nullable|file|mimes:jpeg,png,jpg,mp4|max:10240',
            'media_type' => 'nullable|in:image,video',
            'caption' => 'nullable|string|max:1000',
            'cat_id' => 'nullable|string|max:1000',
        ]);
    
        try {
            DB::beginTransaction();
    
            $storyData = [
                'customer_id' => Auth::id(),
                'cat_id' => $request->cat_id,
                'caption' => $request->caption,
                'expires_at' => now()->addHours(24),
                'media_type' => $request->media_type,
                'media_url' => $request->media_url
            ];
    
            if ($request->hasFile('media_url')) {
                $mediaFile = $request->file('media_url');
                $fileName = time() . '_' . $mediaFile->getClientOriginalName();
                $mediaPath = $mediaFile->storeAs('stories', $fileName, 'public');
                $storyData['media_url'] = $mediaPath;
    
                if ($request->media_type === 'video') {
                    try {
                        $getID3 = new \getID3();
                        $fileAnalysis = $getID3->analyze(storage_path('app/public/' . $mediaPath));
                        
                        if (isset($fileAnalysis['playtime_seconds'])) {
                            $duration = round($fileAnalysis['playtime_seconds']);
                            
                            if ($duration > 60) {
                                Storage::disk('public')->delete($mediaPath);
                                throw new \Exception('Video duration cannot exceed 60 seconds');
                            }
                            
                            $storyData['duration'] = $duration;
                        }
                    } catch (\Exception $e) {
                        Storage::disk('public')->delete($mediaPath);
                        throw new \Exception($e->getMessage());
                    }
                }
            }
    
            $story = Story::create($storyData);
            $story->load('customer');  // Load user before broadcasting
            
            // Transform story data for response
            $response = [
                'id' => $story->id, 
                'caption' => $story->caption ?? '',
                'background_color' => $story->background_color ?? '',
                'media_type' => $story->media_type ?? '',
                'media_url' => $story->media_url ? url('storage/' . $story->media_url) : '',
                'duration' => $story->duration ?? 0,
                'expires_at' => $story->expires_at->diffForHumans(),
                'viewers' => [],
                'views_count' => 0
            ];
    
            broadcast(new StoryEvent($story, 'created'))->toOthers();
    
            DB::commit();
            return response()->json($response, 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 422);
        }

    }


    public function showMystory($id)
    {
        $user = Customers::whereHas('stories', function ($query) {
            $query->where('expires_at', '>', now());
        })
        ->withCount(['stories as stories_count' => function ($query) {
            $query->where('expires_at', '>', now());
        }])
        ->findOrFail($id); // Use `findOrFail` to handle non-existent IDs.
    
        return new MyStoryResource($user); // Ensure proper instantiation of the resource.
    }


    public function myStories()
    {
        $user = auth()->guard('customer')->user();

        $UsersStories = Customers::where('id', '=', $user->id)
       ->whereHas('stories', function ($query) {
        $query->where('expires_at', '>', now());
       })
      ->withCount(['stories as stories_count' => function ($query) {
        $query->where('expires_at', '>', now());
      }])
      ->with(['stories' =>function($query) use ($user){

        $query->withCount(['views as views_count' =>function($query) use ($user){

            $query->where('viewer_id',$user->id);

        }]);
 

      }])
    
      ->get();

        return StoryResource::collection($UsersStories);
    }
    public function destroy(Story $story)
    {
        //if ($story->user_id !== Auth::id()) {
        //    return response()->json(['message' => 'Unauthorized'], 403);
       // }

        // Delete the media file
        $path = str_replace('/storage/', '', $story->media_url);
        Storage::disk('public')->delete($path);

        $story->delete();
        broadcast(new StoryEvent($story, 'delete'))->toOthers();

        broadcast(new MyStoryEvent($story, 'delete'));

        return response()->json([

            'message' => 'story deleted successfully',

            ]);
    }

    public function markAsViewed(Story $story)
    {
        $view = StoryView::firstOrCreate([
            'story_id' => $story->id,
            'viewer_id' => Auth::id()
        ]);
    
       // broadcast(new StoryEvent($story, 'viewed', Auth::id()))->toOthers();
        return response()->json(['message' => 'Story marked as viewed']);
    }

}
