<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ApiCountryDetection;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\ComplainController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\ads\Normalcontroller;
use App\Http\Controllers\RepresentativeController;
use App\Http\Controllers\Api\Popup\PopupController;
use App\Http\Controllers\Api\ConfigurationController;
use App\Http\Controllers\Api\Normalads\CarController;
use App\Http\Controllers\Api\Search\SearchController;
use App\Http\Controllers\Api\Banners\BannerController;
use App\Http\Controllers\Api\Normalads\BikeController;
use App\Http\Controllers\Api\Normalads\CareerController;
use App\Http\Controllers\Api\Normalads\MobileController;
use App\Http\Controllers\Api\Category\CategoryController;
use App\Http\Controllers\Api\Normalads\PropertyController;
use App\Http\Controllers\Api\Search\FiltrationController; 
use App\Http\Controllers\Api\Normalads\NormaladsController;
use App\Http\Controllers\Api\Conversation\MessageController;
use App\Http\Controllers\Api\Customers\auth\LoginController;
use App\Http\Controllers\Api\Commercial\CommercialController;  
use App\Http\Controllers\Api\Customers\auth\RegisterController;
use App\Http\Controllers\Api\Customers\Profile\CountController;
use App\Http\Controllers\Api\Commercial\BikeCommercialController;
use App\Http\Controllers\Api\Commercial\CarCommercialController; 
use App\Http\Controllers\Api\Customers\Profile\ProfileController; 
use App\Http\Controllers\Api\Commercial\CareerCommercialController;
use App\Http\Controllers\Api\Commercial\MobileCommercialController; 
use App\Http\Controllers\Api\Customers\ads\NormalCustomerController;
use App\Http\Controllers\Api\Commercial\PropertyCommercialController;
use App\Http\Controllers\Api\Representatives\RepresntativeController;
use App\Http\Controllers\Api\Customers\Profile\SubscriptionController; 
use App\Http\Controllers\Api\Customers\Notification\NotifcationController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::middleware('auth:sanctum')->group(function () {
    Route::get('conversations', [MessageController::class, 'index']);
    Route::get('conversations/{conversation}', [MessageController::class, 'show']);
    Route::post('messages', [MessageController::class, 'store']);
});

Route::post('/register/email-phone', [RegisterController::class, 'registerEmailPhone']);
Route::post('/register/complete', [RegisterController::class, 'completeRegistration']);


Route::post('/login/customers',[LoginController::class , 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/total-comments-favorites', [NormalCustomerController::class,'getTotalCommentsAndFavorites']);

    
    Route::post('logout/customers', [LoginController::class, 'logout']);
    

    Route::apiResource('ApiProfile',ProfileController::class);

    Route::get('/subscription/plans', [SubscriptionController::class, 'showPlans']);

    Route::post('/subscription/select', [SubscriptionController::class, 'selectPlan']);

    Route::post('/ApiAddcomment', [CommentController::class, 'store']);

    Route::post('/ApiAddcomplain', [ComplainController::class, 'store']);

    Route::get('/ApiActiveComment', [CommentController::class, 'activate_comment']);

    Route::get('/subscription/customerPlan', [SubscriptionController::class, 'customerPlan']);

    Route::apiResource('ApiNormalAds', NormaladsController::class);

    Route::post('/ads/favorite', [FavoriteController::class, 'toggleFavorite']);
    
    Route::delete('/normal-ads/{normal}/favorite', [FavoriteController::class, 'removeFavorite']);
    Route::get('/favorites', [FavoriteController::class, 'allFavorite']);
 

   

    
    Route::apiResource('ApiCars',CarController::class);

    Route::get('/carFeaturesApi', [CarController::class, 'carFeatures']);

    Route::get('/carBrandsApi', [CarController::class, 'carBrands']);

    
    
    Route::apiResource('ApiBikes',BikeController::class);

    Route::get('/bikeFeaturesApi', [BikeController::class, 'bikeFeatures']);

    Route::apiResource('ApiProperty',PropertyController::class);

    Route::get('/HouseFeaturesApi', [PropertyController::class, 'houseFeatures']);


    Route::apiResource('ApiMobile',MobileController::class);

    Route::apiResource('ApiCareer',CareerController::class);



    Route::apiResource('ApiCommercialAds', CommercialController::class);

    Route::apiResource('ApiPopupAds', PopupController::class);


    Route::apiResource('ApiAllNormal', Normalcontroller::class);

    Route::apiResource('ApiBanners', BannerController::class);


    Route::apiResource('ApiRepresntatives', RepresntativeController::class);



    Route::apiResource('ApiCustomer/Ads', NormalCustomerController::class);

    

    Route::post('/register/social', [RegisterController::class, 'social']);

    Route::apiResource('ApiSearch',SearchController::class);

    Route::get('/categories/{id}/related', [SearchController::class, 'getRelatedItems'])->name('categories.related');
    
    Route::get('/search-ads', [SearchController::class, 'searchAds']);
    
    
    
    Route::get('/mainCategory',[CategoryController::class , 'MainCategory']);
    
    Route::get('/SubCategory/{cat_id}',[CategoryController::class , 'SubCategory']);
    
    Route::get('filters/{cat_id}', [FiltrationController::class, 'showFilters']);
    
    Route::post('apply-filters/{cat_id}', [FiltrationController::class, 'applyFilters']);
    
    Route::get('ApiGetRelatedAds/{cat_id}', [FiltrationController::class, 'getRelatedAds']);

    Route::get('selectedCustomerCurrency', [CurrencyController::class, 'selectedCustomerCurrency']);

    Route::post('/Apicurrency',[CurrencyController::class,'store']);

    Route::get('/ApiAllcurrency',[CurrencyController::class,'index']);


    Route::resource('ApiCountries', CountryController::class);




    Route::get('ApiselectedCountry', [CountryController::class, 'selectedCountry']);

    Route::get('countViewAds', [CountController::class, 'countViewAds']);

    Route::get('countAds', [CountController::class, 'countAds']);

});

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotifcationController::class, 'getNotifications']);
        Route::get('/unread', [NotifcationController::class, 'getUnreadNotifications']);
        Route::post('/{id}/read', [NotifcationController::class, 'markAsRead']);
        Route::post('/read-all', [NotifcationController::class, 'markAllAsRead']);
        Route::post('/close-notification', [NotifcationController::class, 'close_notification']);
        Route::post('/open-notification', [NotifcationController::class, 'open_notification']);

    });
}); 
 


Route::apiResource('ApiConfiguration',ConfigurationController::class);

Route::get('IsEmptyfilters/{cat_id}', [FiltrationController::class, 'isFilter']);



