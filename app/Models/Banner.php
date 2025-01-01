<?php

namespace App\Models;

use App\Models\Country;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Banner extends Model
{
    use HasFactory;

    protected $guarded=[];

    public $timestamps = false;

    
    public function category()
    {
        return $this->belongsTo(Category::class,'cat_id');
    }
    
    protected static function boot()
    {
        parent::boot();
    
        static::addGlobalScope('country', function (Builder $builder) {
            $customer = Auth::guard('customer')->user();
    
            if ($customer && $customer->country_id) {
                $builder->where('country_id', $customer->country_id);
            }
            // If no country_id, do nothing (no scope applied)
        });
    }

    public function country() {

        return $this->belongsTo(Country::class);
    }


    
}
