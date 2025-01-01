<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\log;

use Illuminate\Http\Resources\Json\JsonResource;

class ShowNormalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $AuthCustomer = Auth::guard('customer')->user();
        $currencyCode = $AuthCustomer && $AuthCustomer->currency ? $AuthCustomer->currency->code : 'USD';
        $locale = App::getLocale();

        return [
            'id' => $this->id,
            'title' => __($this->title),
            'subcategory' => __($this->category->title),
            'main_category' => optional($this->category->parent)->title ? __($this->category->parent->title) : null,
            'description' => __($this->description),
            'price' => number_format($this->price, 2) . ' ' . $currencyCode,
            'currency_code' => $currencyCode,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'location' => __($this->address),
            'is_featured' => $this->is_featured,
            'Featured photo' => asset('storage/'.$this->photo),
            'count_views' => $this->views_count,
            'created_time' => Carbon::parse($this->created_at)->diffForHumans(),
            'images' => $this->images->map(function($image) {
                return [
                    'url' => asset('storage/' . $image->image_path)
                ];
            }),

            'comments_count' => $this->comments->where('status', 1)->count(),
            'comments' => $this->comments
                ->where('status', 1)
                ->map(function($comment) use ($locale) {
                    return [
                        'id' => $comment->id,
                        'text' => __($comment->text),
                        'customer' => [
                            'name' => $comment->customer->name,
                            'id' => $comment->customer->id
                        ],
                        'created_at' => Carbon::parse($comment->created_at)->diffForHumans()
                    ];
                }),

            'customer' => [
                'name' => $this->customer->name,
                'photo' => $this->customer->photo ? asset('storage/' . $this->customer->photo) : asset('assets/avatar.png'),
                'member_since' => Carbon::parse($this->customer->created_at)->diffForHumans(),
                'instagram' => $this->customer->instgram,
                'twitter' => $this->customer->twitter,
                'linkedin' => $this->customer->linkedIn,
            ],

            'cars' => $this->cars ? [
                'color' => __($this->cars->color),
                'year' => $this->cars->year,
                'kilo_meters' => $this->cars->kilo_meters,
                'fuel_type' => __($this->cars->fuel_type),
                'brand' => __($this->cars->brands->title),
                'features' => $this->cars->features->isNotEmpty() ? $this->cars->features->map(function($feature) {
                    return [
                        'title' => __($feature->title),
                    ];
                })->toArray() : null,
            ] : null,

            'bikes' => $this->bikes ? [
                'model' => __($this->bikes->model),
                'year' => $this->bikes->year,
                'kilo_meters' => $this->bikes->kilo_meters,
                'status' => __($this->bikes->status),
                'features' => $this->bikes->features->isNotEmpty() ? $this->bikes->features->map(function($feature) {
                    return [
                        'title' => __($feature->title),
                    ];
                })->toArray() : null,
            ] : null,

            'houses' => $this->houses ? [
                'room_no' => $this->houses->room_no,
                'area' => $this->houses->area,
                'view' => __($this->houses->view),
                'building_no' => $this->houses->building_no,
                'history' => __($this->houses->history),
                'features' => $this->houses->features->isNotEmpty() ? $this->houses->features->map(function($feature) {
                    return [
                        'title' => __($feature->title),
                    ];
                })->toArray() : null,
            ] : null,

            'mobiles' => $this->mobiles ? [
                'storage' => $this->mobiles->storage,
                'ram' => $this->mobiles->ram,
                'screen_size' => $this->mobiles->disply_size,
                'sim_no' => $this->mobiles->sim_no,
            ] : null,
        ];
    }
}