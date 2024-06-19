<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    //
    public function updateFcmToken(Request $request) {
        $request->validate([
            'fcm_token' => 'required'
        
        ]);
        $user = auth()->user();
        $user->fcm_token = request('fcm_token');
        $user->save();
        return response()->json(['message' => 'FCM Token updated successfully']);
    }
}
