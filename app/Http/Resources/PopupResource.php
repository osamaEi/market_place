<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PopupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [

            'id' =>$this->id,
            'title' => __($this->name),
            'description' => __($this->description),
            'category' => __($this->category->title),
            'photo' => asset('storage/'.$this->photo),
            'created_time' => Carbon::parse($this->created_at)->diffForHumans(),




        ];


    }
}
