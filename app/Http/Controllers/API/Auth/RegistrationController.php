<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{

    public function __invoke(UpdateProfileRequest $request)
    {
        $data = collect($request->validated());

        $user = User::create($data->except('address')->toArray());
        $user->assignRole('user');
        $address = new Address($data->get('address'));
        $user->address()->save($address);
        $user->load('address.country');
        return UserResource::make($user)->response()->setStatusCode(200);
    }
}