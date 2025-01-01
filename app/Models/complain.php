<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class complain extends Model
{
    use HasFactory;

    protected $guarded=[];


   
    public function complainable()
    {
        return $this->morphTo(); // This method should match the name used in morphMany
    }

    public function customer(){

        return $this->belongsTo(Customers::class,'customer_id');
    }



}
