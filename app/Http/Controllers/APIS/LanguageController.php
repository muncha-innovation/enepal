<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Get all languages
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $languages = Language::orderBy('name')->get();
        
        return response()->json([
            'success' => true,
            'data' => $languages
        ]);
    }
}