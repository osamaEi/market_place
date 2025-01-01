<?php
namespace App\Services;

use App\Services\AdLimitServices;
use App\Models\NormalAds;
use App\Jobs\TranslateText;
use Illuminate\Http\Request;
use App\Models\ImageNormalAds;
use Illuminate\Support\Facades\Auth;

class NormalAdsService
{
public function storeNormalAd(Request $request)
    {
        $adLimitService = new AdLimitServices();

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'cat_id' => 'required|integer',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'address' => 'required',
            'images*.'=>'nullable',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

     

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $originalName = $file->getClientOriginalName(); 
            $validatedData['photo'] = $file->storeAs('photos', $originalName, 'public');
        }
        

        $customer = Auth::guard('customer')->user();

        

        $countryId = $customer ? $customer->country_id :$request->session()->get('country_id');


        $normalAd = new NormalAds($validatedData);
        $normalAd->country_id = $countryId;
        $normalAd->customer_id = $customer->id;
        $normalAd->latitude = $request->latitude;
        $normalAd->longitude = $request->longitude;
        $normalAd->is_active = false;


        if (!$adLimitService->canPostAd('normal')) {

            $normalAd->is_featured = null;

        } else {

            $normalAd->is_featured = true;
            
            $adLimitService->updateAdLimits('normal');

        }


        
        


        $normalAd->is_active = false;

        
        $normalAd->save();

        if ($request->hasFile('images')) {
            $images = $request->file('images');
            foreach ($images as $image) {
                $imagePath = $image->store('normal_ads_images', 'public');

                ImageNormalAds::create([
                    'normal_ads_id' => $normalAd->id,
                    'image_path' => $imagePath,
                ]);
            }
        }

        return $normalAd;
    }


    
    protected function translateAndSave(array $inputs, $operation)
{
    $languages = ['en', 'fr', 'es', 'ar', 'de', 'tr', 'it', 'ja', 'zh', 'ur'];

    foreach ($inputs as $key => $value) { 
        if (is_string($value) && !empty($value)) {
            dispatch(new TranslateText($key, $value, $languages));
        }
    }
}

}