<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class CommercialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = App::getLocale(); 
         
        // Debug information
        Log::info('Translation Debug', [ 
            'locale' => $locale,
            'title' => $this->title,
            'has_json_translations' => file_exists(resource_path("lang/{$locale}.json")),
        ]);

        return [
            'id' => $this->id,
            'title' => __($this->title),  // Fixed from ** to __
            'description' => __($this->description),  // Fixed from ** to __
            'category' => __($this->category->title),  // Also translate category
            'photo' => asset('storage/'.$this->photo_path),
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_featured' => $this->is_featured,

            'created_time' => Carbon::parse($this->created_at)->diffForHumans(),
            'count_views' => $this->views_count,
            'comments_count' => $this->comments->where('status', 1)->count(),
            'comments' => $this->comments
                ->where('status', 1)
                ->map(function($comment) use ($locale) {
                    return [
                        'id' => $comment->id,
                        'text' => $comment->text,
                        'customer' => [
                            'name' => $comment->customer->name,
                            'id' => $comment->customer->id
                        ],
                        'created_at' => Carbon::parse($comment->created_at)->diffForHumans()
                    ];
                })
        ];
    }
}