<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowStoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [ 
 
            
                'customer_name' => $this->customer->name,
                'customer_photo' => url('storage/'.$this->customer->photo),
                'category' => $this->category->title,
                'caption' => $this->caption ?$this->caption : '' ,
                'media_type' => $this->media_type ? $this->media_type : '',
                'media_url' => $this->media_url ?  'media_url' => url('storage/'.$story->media_url),  : '',
                'expire_at' => $this->expire_at,



            
        ];
    }
}
