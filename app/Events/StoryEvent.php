<?php

namespace App\Events;

use App\Models\Story;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; 
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;  

class StoryEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
 
    public $story; 
    public $action; 

    public function __construct(Story $story, $action)
    {
        $this->story = $story;
        $this->action = $action;
    }

    public function broadcastOn()
    {
        // Log::debug('Broadcasting config:', [
        //     'key' => config('broadcasting.connections.reverb.key'),
        //     'host' => config('broadcasting.connections.reverb.host'),
        //     'port' => config('broadcasting.connections.reverb.port')
        // ]);
        return new Channel('stories');
    }

    public function broadcastAs()
{
    return 'story.created';
}


    public function broadcastWith()
    {
        return [
            'story' => $this->story,
            'event' => $this->action
        ];
    }
}