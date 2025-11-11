<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch application locale
     */
    public function switch($locale)
    {
        // Check if locale is valid
        if (in_array($locale, ['en', 'es'])) {
            Session::put('locale', $locale);
            app()->setLocale($locale);
        }

        return back();
    }

    
}
