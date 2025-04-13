<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryStoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_name' => $this->customer->name,
            'customer_photo' => url('storage/'. $this->customer->photo),
            'media_url' => url('storage/'. $this->media_url),
            'media_type' => $this->media_type,
            'caption' => $this->caption,
            'expires_at' => $this->expires_at,
            'duration' => $this->duration,
            'background_color' => $this->background_color
        ];
    }
}
