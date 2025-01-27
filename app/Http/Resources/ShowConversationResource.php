<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'sender_name' =>$this->sender->name,
             'sender_photo' =>url('storage/'.$this->sender->photo),


            'content' =>$this->content,
            'reciver_name' =>$this->receiver->name,
             'reciver_photo' =>url('storage/'.$this->receiver->photo),




        ];
    }
}
