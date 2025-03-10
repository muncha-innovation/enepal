<?php

namespace App\Http\Controllers\ApiAuth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{

    public function __invoke(StoreUserRequest $request)
    {
        $data = collect($request->validated());
        $exists = User::where('email', $data->get('email'))->exists();
        if($exists) {
            return response()->json(['message' => trans('User already exists. Please login to continue',[],$request->get('lang','en'))], 400);
        }
        $user = User::create($data->except(['address','original_password'])->toArray());

        $user->assignRole('user');
        if($data->has('address')) {
            $address = new Address($data->get('address'));
            $user->addresses()->save($address);
            $user->load('addresses.country');
        }
        return UserResource::make($user)->response()->setStatusCode(200);
    }
}
