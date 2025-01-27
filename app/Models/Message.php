<?php

namespace App\Models;

use App\Models\Customers;
use App\Models\Conversation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'content',
        'is_read',
        'conversation_id'
    ];

    public function sender()
    {
        return $this->belongsTo(Customers::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Customers::class, 'receiver_id');
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
