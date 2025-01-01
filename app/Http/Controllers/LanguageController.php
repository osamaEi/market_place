<?php
namespace App\Http\Controllers;

use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Stichoza\GoogleTranslate\GoogleTranslate;

class LanguageController extends Controller
{
    /**
     * Display the language selection page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('lang');
    }

    /**
     * Change the language for the session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function change(Request $request)
    {
        App::setLocale($request->lang);
        session()->put('locale', $request->lang);
        return redirect()->back();
    }

  
    public function translate(Request $request)
    {
        $text = $request->input('text');

        if (!$text) {
            return redirect()->back()->with('error', 'Please provide text to translate.');
        }

        $languages = ['en', 'fr', 'es', 'ar', 'de', 'tr', 'it', 'ja', 'zh', 'ur'];

        $translations = [];
        foreach ($languages as $lang) {
            $translations[$lang] = GoogleTranslate::trans($text, $lang);
            $this->saveTranslation($lang, $request->input('text'),$translations[$lang]);
        }

        return view('lang')->with('translations', $translations);
    }

  
    private function saveTranslation($lang,$inputs, $translatedText)
    {
        $path = resource_path("lang/{$lang}.json");

        $translations = file_exists($path) ? json_decode(file_get_contents($path), true) : [];

        $translations[$inputs] = $translatedText;

        file_put_contents($path, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
