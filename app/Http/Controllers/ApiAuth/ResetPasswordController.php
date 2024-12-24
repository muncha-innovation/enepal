<?php

namespace App\Http\Controllers\ApiAuth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
	public function __invoke(Request $request)
	{
		$lang = $request->query('lang') ?? 'en';

		$request->validate([
			'email' => ['required', 'email'],
		]);

		$status = Password::sendResetLink(
			$request->only('email')
		);

		if ($status == Password::RESET_LINK_SENT) {
			return response()->json([
				'message' => trans($status, [], $lang)
			]);
		} else {
			return response()->json([
				'message' => trans($status, [], $lang)
			], 400);
		}
	}
}