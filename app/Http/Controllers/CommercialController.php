<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Jobs\TranslateText;
use App\Models\CommercialAd;
use Illuminate\Http\Request;
use App\Exports\CommercialExport;
use App\Services\FirebaseService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class CommercialController extends Controller
{

    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function index(Request $request)
    {
        $categories =Category::all();


        $query = CommercialAd::query();



        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', '%' . $search . '%');
        }
        if ($request->filled('is_featured')) {
            $query->where('is_featured',$request->input('is_featured'));
        }
    


        if ($request->has('cat_id') && $request->category_id != '') {
            $categoryId = $request->input('cat_id');
            $query->whereHas('cat_id', function($q) use ($categoryId) {
                $q->where('id', $categoryId);
            });
        }

        if ($request->has('is_active') && $request->is_active !== '') {
            $isActive = $request->input('is_active');
            $query->where('is_active', $isActive);
        }

        $commercialAds = $query->withoutGlobalScope('country')->paginate(10);

        return view('backend.commercialads.index', compact('commercialAds','categories'));
    }
    public function export()
    {
        return Excel::download(new CommercialExport, 'commercial.xlsx');
    }
    public function create()
    {
       $categories = Category::WhereNull('parent_id')->get();


        return view('backend.commercialads.create', compact('categories'));
    }

  public function show($id) {

        $commercial = CommercialAd::find($id);



        return view('backend.commercialads.show',compact('commercial'));


    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'cat_id' => 'required|integer',
            'whatsapp' => 'required',
            'phone' => 'required',
        ]);




        if ($request->hasFile('photo_path')) {
            $photoPath = $request->file('photo_path')->store('commercial', 'public');
        }


        $countryId = $request->session()->get('country_id');
        $ad = new CommercialAd();
        $ad->title = $request->title;
        $ad->description = $request->description;
        $ad->phone = $request->phone;
        $ad->whatsapp = $request->whatsapp;
        $ad->photo_path =  $photoPath;
        $ad->country_id =  $countryId;
        $ad->cat_id= $request->cat_id;



        $ad->save();

        $this->translateAndSave($request->all(), 'store');


        return redirect()->route('commercialads.index')->with('success', __('Commercial Ad created successfully!'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'cat_id' => 'required|integer',
            'whatsapp' => 'required',
            'phone' => 'required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Find the ad by ID
        $ad = CommercialAd::findOrFail($id);

        // Update basic fields
        $ad->title = $request->title;
        $ad->description = $request->description;
        $ad->phone = $request->phone;
        $ad->whatsapp = $request->whatsapp;

        // Handle is_active field
        $ad->is_active = $request->has('is_active') ? 1 : 0;




        if ($request->hasFile('photo')) {
            // Delete old photo if exists

            $photoPath = $request->file('photo')->store('commercial', 'public');

            $ad->photo_path = $photoPath;
        }

        // Save the updated ad
        $ad->save();

        // Translate and save if needed
        $this->translateAndSave($request->all(), 'update');

        return redirect()->back()->with('success', __('Commercial Ad updated successfully!'));
    }



    public function destroy($id)
    {
        $model = CommercialAd::findOrFail($id);
        $model->delete();

        return redirect()->back();
    }



    public function toggleStatus(CommercialAd $ad)
    {
        $ad->is_active = !$ad->is_active; 
        $ad->save();
    
        try {
            $this->firebaseService->sendNotification(
                $ad->customer->fcm_token,
                'congratulations',
                'Ad ' . $ad->title . ' has been activated',
                $request->data ?? [],  // Optional data
                $ad->customer->id  // Pass the customer ID as the 5th argument
            );
    
            return redirect()->back()->with('success', __('Ad status updated successfully!'));
    
        } catch (\Exception $e) {
            return redirect()->back()->with('warning', __('Ad status updated successfully! but notification not sent: ' . $e->getMessage()));
        }
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
