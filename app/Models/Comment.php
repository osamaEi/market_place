<?php

namespace App\Models;

use App\Models\Customers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function customer(){

        return $this->belongsTo(Customers::class,'customer_id');
    }

}
