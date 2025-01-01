<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Helpers\ConvertApiCurrency;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\log;
use Illuminate\Http\Resources\Json\JsonResource;

class NormalAdResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $AuthCustomer = Auth::guard('customer')->user();
        $currencyCode = $AuthCustomer && $AuthCustomer->currency ? $AuthCustomer->currency->code : 'USD';
        $locale = App::getLocale();

        Log::info('Translation Debug', [ 
            'locale' => $locale,
            'title' => $this->title,
            'has_json_translations' => file_exists(resource_path("lang/{$locale}.json")),
        ]);
        return [
            'id' => $this->id,
            'title' => __($this->title),
            'subcategory' => __($this->category->title),
            'main_category' => optional($this->category->parent)->title ? __($this->category->parent->title) : null,
            'price' => $this->price,
            'Featured_photo' => asset('storage/'.$this->photo),
            'currency_code' => $currencyCode,
            'location' => __($this->address), // Translate location/address
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'created_time' => Carbon::parse($this->created_at)->diffForHumans(),
            'count_views' => $this->views_count,
            'is_featured' => $this->is_featured,

            'comments_count' => $this->comments->where('status', 1)->count(),
            'comments' => $this->comments
                ->where('status', 1)
                ->map(function($comment) use ($locale) {
                    return [
                        'id' => $comment->id,
                        'text' => __($comment->text), // Translate comment text
                        'customer' => [
                            'name' => $comment->customer->name,
                            'id' => $comment->customer->id
                        ],
                        'created_at' => Carbon::parse($comment->created_at)->diffForHumans()
                    ];
                }),
            // Add additional translated metadata if needed
            'status' => __('ad.status.' . $this->status), // Assuming you have status translations
            'condition' => $this->condition ? __('ad.condition.' . $this->condition) : null,
        ];
    }
}