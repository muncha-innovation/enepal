<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    //
    public function states(Request $request, Country $country) {
        
        return response()->json($country->states);
    }

    public function index() {
        return response()->json(Country::all());
    }
}
