<?php

namespace App\Http\Controllers\ApiAuth;

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
    $lang = $request->query('lang') ?? 'en';

    $request->validate([
        'email' => ['required'],
        'password' => ['required'],
    ]);

    $user = User::with('addresses')->where('email', $request->email)->first();

    if (!$user) {
        return response()->json([
            'message' => trans('User not found', [], $lang)
        ], 400);
    }

    if (!$user->is_active) {
        return response()->json([
            'message' => trans('User is not active', [], $lang)
        ], 400);
    }

    if (!Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => trans('The provided credentials are incorrect.', [], $lang),
        ], 400);
    }

    // Assign translated role
    $user->role = trans($user->getRoleNames()[0], [], $lang);

    // Load related models
    $user->load('addresses.country');

    // Create token
    $token = $user->createToken('enepal')->plainTextToken;

    // Return token and user data in same format as registration
    return response()->json([
        'token' => $token,
        'user' => UserResource::make($user),
    ], 200);
}

}
