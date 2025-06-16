<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CountryController extends Controller
{
    //
    public function states(Request $request, Country $country)
    {
        $states = Cache::rememberForever($country->id . '_states', function () use ($country) {
            return $country->states;
        });
        return response()->json($states);
    }

    public function index()
    {
        $countries = Cache::rememberForever('countries_with_states', function () {
            return Country::with('states')->get();
        });

        return response()->json([
            'countries' => $countries
        ]);
    }
}
