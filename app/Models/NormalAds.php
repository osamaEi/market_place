<?php

namespace App\Models;

use App\Models\Comment;
use App\Models\Country;
use App\Models\Category;
use App\Models\complain;
use App\Models\Customers;
use Modules\Car\Models\Cars;
use Modules\Bike\Models\Bike;
use Modules\House\Models\House;
use Modules\Device\Models\Device;
use Modules\Career\Models\Careers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Modules\Electronics\Models\Mobiles;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NormalAds extends Model
{
    use HasFactory;

    protected $guarded = [];


    protected $table="normal_ads";

    public function customer(){

        return $this->belongsTo(Customers::class,'customer_id');
    }
  
    
    public function country() {

        return  $this->belongsTo(Country::class,'country_id');
    }
    public function category(){

        return $this->belongsTo(Category::class,'cat_id');
    }

    public function images() {

        return $this->hasMany(ImageNormalAds::class,'normal_ads_id');
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

public function scopeFeatured(Builder $query)
{
    return $query->orderBy('is_featured', 'desc')
                 ->orderBy('created_at', 'desc');
}

public function cars()
{

    return $this->hasOne(Cars::class,'normal_id');
}

public function bikes()

{

    return $this->hasOne(Bike::class,'normal_id');
}
public function houses()

{

    return $this->hasOne(House::class,'normal_id');
}
public function careers()

{

    return $this->hasOne(Careers::class,'normal_id');
}

public function mobiles()

{

    return $this->hasOne(Mobiles::class,'normal_id');
}
public function devices()

{

    return $this->hasOne(Device::class,'normal_id');
}

public function favoritedBy()
{
    return $this->belongsToMany(customers::class, 'favorites')->withTimestamps();
}

public function comments()
{
    return $this->morphMany(Comment::class, 'commentable');
}


    public function complains()
    {
        return $this->morphMany(complain::class, 'complainable'); // Use 'complainable' for consistency
    }

}
