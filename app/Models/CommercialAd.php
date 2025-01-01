<?php

namespace App\Models;

use App\Models\Comment;
use App\Models\Country;
use App\Models\Category;
use App\Models\complain;
use App\Models\Customers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommercialAd extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'photo_path'];

    public function category()
    {
        return $this->belongsTo(Category::class,'cat_id');
    }
    public function scopeFeatured(Builder $query)
{
    return $query->orderBy('is_featured', 'desc')
                 ->orderBy('created_at', 'desc');
}

    public function customer() {

        return $this->belongsTo(Customers::class);
    }

    
 
    public function country() {

        return  $this->belongsTo(Country::class,'country_id');
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

public function comments()
{
    return $this->morphMany(Comment::class, 'commentable');
}

public function complains()
    {
        return $this->morphMany(complain::class, 'complainable'); // Use 'complainable' for consistency
    }


}
