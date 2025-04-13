<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, // Customer ID
            'name' => $this->name, // Customer name
            'photo' => url('storage/'.$this->photo), // Customer name
            'email' => $this->email, // Customer email
            'stories_count' => $this->stories_count, // Total active stories count
            'stories' => $this->stories->map(function ($story) {
                return [
                    'id' => $story->id, // Story ID
                    'caption' => $story->caption, // Story caption
                    'media_url' => url('storage/'.$story->media_url), // URL of the story media
                    'media_type' => $story->media_type, // Type of media (image/video)
                    'duration' => $story->duration, // Duration of the video (if applicable)
                    'expires_at' => $story->expires_at, // Expiration time of the story
                    'views_count' => $story->views_count, // Number of views for the story
                ];
            }),
        ];
    }
}