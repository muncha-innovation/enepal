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
    public function states(Request $request, Country $country) {
        $states = Cache::remember($country->id.'_states', 3600, function() use ($country) {
            return $country->states;
        });
        return response()->json($states);
    }

    public function index() {
    $countries = Cache::remember('countries_with_states', 3600, function () {
        return CountryResource::collection(Country::with(['states'])->get());
    });

    return response()->json([
        'countries' => $countries
    ]);
}
}
