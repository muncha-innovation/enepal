<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RapidApiController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'postal_code1' => ['required', 'regex:/^\d{3}/'],
            'postal_code2' => ['required', 'regex:/^\d{4}$/']
        ]);

        $response = Http::withHeaders([
            'content-type' => 'application/json',
            'x-rapidapi-host' =>  config('services.rapidapi.host'),
            'x-rapidapi-key' => config('services.rapidapi.key')
        ])->post('https://geekfeed-search-japanese-postcode-v1.p.rapidapi.com/search-address', [
            'zipcode' => $request->postal_code1 .  $request->postal_code2
        ]);

        $maindata = json_decode(json_encode($response->object()), true);

        if (empty($maindata)) {
            return response()->json([
                'message' => trans('Postal code is not correct'),
                'status' => 201,
            ]);
        }
        // if ($maindata[0] == 'Limit Exceeded') {
        //     return 0;
        // }

        return collect(Arr::flatten($maindata, 3))->mapWithKeys(function ($item) {
            return [$item['Name'] => $item['Value']];
        })->toArray();
    }
}