<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Constraint\Count;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $lang = $request->query('lang');
        $lang = $lang ?? 'en';


        $request->validate([
            'email' => ['required'],
            'password' => ['required'],
        ]);
        $tokenName = 'enepal';

        $user = User::where('email', $request->email)->with(['addresses'])->first();
        
        if (!$user) {
            return response()->json([
                'message' => trans('User not found', [], $lang)
            ], 400);
        }
        if (!$user->active) {
            return response()->json([
                'message' => trans('User is not active', [], $lang)
            ], 400);
        }
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => trans('The provided credentials are incorrect.', [], $lang),
            ], 400);
        }
        $user->role = trans($user->getRoleNames()[0], [], $lang);
        $user->load('addresses.country');
        return UserResource::make($user)->response()->setStatusCode(200);
    }
}
