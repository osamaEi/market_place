<?php

namespace App\Models;

use App\Models\Message;
use App\Models\Customers;
use Illuminate\Database\Eloquent\Model;

// app/Models/Conversation.php
class Conversation extends Model
{
    protected $fillable = [
        'customer_one',
        'customer_two',
        'last_message_at'
    ];

    protected $dates = [
        'last_message_at'
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function userOne()
    {
        return $this->belongsTo(Customers::class, 'customer_one');
    }

    public function userTwo()
    {
        return $this->belongsTo(Customers::class, 'customer_two');
    }

    public function getOtherUser($userId)
    {
        return $this->customer_one == $userId ? $this->userTwo : $this->userOne;
    }
}