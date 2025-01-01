<?php

namespace App\Models;

use App\Models\Bill;
use App\Models\Banner;
use App\Models\Country;
use App\Models\Currency;
use App\Models\PopUpAds;
use App\Models\NormalAds;
use App\Models\CommercialAd;
use Modules\Car\Models\Cars;
use Modules\Bike\Models\Bike;
use Modules\House\Models\House;
use Laravel\Sanctum\HasApiTokens;
use App\Models\CustomerSubscription;
use Illuminate\Database\Eloquent\Model;
use Modules\Electronics\Models\Mobiles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customers extends Model implements Authenticatable
{
    use HasFactory,HasApiTokens,Notifiable;

    protected $guarded=[];

    protected $guard = 'customer';


    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function getAuthPasswordName()
    {
        return 'password';
    }


    public function NormalAds(){

        return $this->hasMany(NormalAds::class,'customer_id');
    }
    public function country(){

        return $this->belongsTo(Country::class,'country_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class); 
    }
    
    
    public function CommericalAds(){

        return $this->hasMany(CommercialAd::class,'customer_id');
    }

    public function popUpAds(){

        return $this->hasMany(PopUpAds::class,'customer_id');
    }
    
    public function banners(){

        return $this->hasMany(Banner::class,'customer_id');
    }
    
    public function bills(){

        return $this->hasMany(Bill::class,'customer_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(CustomerSubscription::class,'customer_id');
    }

    public function currentSubscription()
    {
        return $this->hasOne(CustomerSubscription::class, 'customer_id') 
            ->where('end_date', '>', now())
            ->orderBy('end_date', 'desc');
    }


    public function favorites()
{
    return $this->belongsToMany(NormalAds::class, 'favorites')->withTimestamps();
}

public function addFavorite(NormalAds $normalads)
{
    $this->favorites()->attach($normalads->id);
}

public function removeFavorite(NormalAds $product)
{
    $this->favorites()->detach($normalads->id);
}

public function toggleFavorite(NormalAds $normalads)
{
    $this->favorites()->toggle($normalads->id);
}

public function hasFavorited(NormalAds $normalads)
{
    return $this->favorites()->where('normal_ads_id', $normalads->id)->exists();
}


}

