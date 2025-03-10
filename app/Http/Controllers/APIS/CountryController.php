<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    //
    public function states(Request $request, Country $country) {
        
        return response()->json($country->states);
    }

    public function index() {
        return response()->json([
            'countries' => CountryResource::collection(Country::with(['states'])->get())
        ]);
    }
}
