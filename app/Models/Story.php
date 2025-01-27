<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Customers;
use App\Models\StoryView;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{

    protected $guarded =[];

    public function customer() {

        return $this->belongsTo(Customers::class,'customer_id');


    }

    
    public function views()
    {
        return $this->hasMany(StoryView::class);
    }
    
    public function category() {

        return $this->belongsTo(Category::class,'cat_id');


    }
    
}
