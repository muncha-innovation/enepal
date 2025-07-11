<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Http\Resources\BusinessResource;
use App\Http\Resources\CategoryResource;
use App\Models\Business;
use App\Models\Category;
use App\Models\Country;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class AddressPreferencesController extends Controller
{
    public function fetch(Request $request): array
    {
        $data = [];
        $data['countries'] = Cache::rememberForever('countries_with_states', function () {
            return Country::with('states')->get();
        });

        $data['addresses'] = AddressResource::collection(resource: auth()->user()->addresses()->get());


        // todo: refactor
        $data['businesses'] = BusinessResource::collection(resource: Business::following()->with(relations: ['type'])->get());
        $data['categories'] = CategoryResource::collection(resource: NewsCategory::all());
        return $data;
    }
    public function updateAddress(Request $request): JsonResponse
    { {
            $request->validate(rules: [
                'addresses' => 'required',
            ]);
            $addresses = $request->addresses;
            $user = auth()->user();
            foreach ($addresses as $type => $addressData) {
                // todo: refactor
                // make sure array key exists and set default otherwise
                $user->addresses()->updateOrCreate(
                    attributes: ['address_type' => $type],
                    values: [
                        'country_id' => $addressData['country'],
                        'state_id' => $addressData['state'] ?? null,
                        'city' => $addressData['city'] ?? '',
                    ]
                );
            }

            return response()->json(data: ['message' => 'Addresses updated successfully']);
        }
    }
}
