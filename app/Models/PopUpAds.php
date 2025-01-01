<?php

namespace App\Models;

use App\Models\Country;
use App\Models\Category;
use App\Models\Customers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PopUpAds extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function category(){

        return $this->belongsTo(Category::class,'cat_id');
    }

    public function customer() {

        return $this->belongsTo(Customers::class);
    }

    
    public function country() {

        return  $this->belongsTo(Country::class);
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
    
    public function scopeActive(Builder $query)
{
    return $query->where('is_active', 1);
}

}
