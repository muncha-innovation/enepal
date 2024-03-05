<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        $tokenName = 'goko_denko';

        $user = User::where('email', $request->email)->orWhere('user_name',$request->email)->with(['address','departments'])->first();
        $departments = $user->departments->pluck('name')->toArray();
        unset($user->departments);
        $user->departments = $departments;
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
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => trans('The provided credentials are incorrect.', [], $lang),
            ], 400);
        }
        $user->role = trans($user->getRoleNames()[0], [], $lang);
        if($user->address?->country) {
            $user->address->country =$user->address->country_name;
        }
        $user->image  = $user->full_path;
        return response()->json([
            'token' => $user->createToken($tokenName)->plainTextToken,
            'user' => collect($user)->except(['email_verified_at', 'created_at', 'updated_at']),
        ], 200);
    }
}
