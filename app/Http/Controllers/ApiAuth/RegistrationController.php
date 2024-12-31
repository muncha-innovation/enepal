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

        $user = User::create($data->except(['address','original_password'])->toArray());
        $user->assignRole('user');
        $address = new Address($data->get('address'));
        $user->addresses()->save($address);
        $user->load('address.country');
        return UserResource::make($user)->response()->setStatusCode(200);
    }
}
