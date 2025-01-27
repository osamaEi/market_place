<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $id = auth()->id();

        $other_user = $this->getOtherUser($id);



        return [


            'id' =>$this->id,
            'other_user_name' =>  $other_user->name,

            'other_user_photo' => url('storage/'. $other_user->photo),

            'last_message_time' => \Carbon\Carbon::Parse($this->last_message_at)->diffForHumans()






            


        ];
    }
}
