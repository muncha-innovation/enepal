<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BusinessType;
use Illuminate\Http\Request;

class BusinessController extends Controller
{

    public function getBusinessTypes(Request $request) {
        
    }

    public function getBusinessType(Request $request) {
        $request->validate([
            'id' => 'required|integer'
        ]);
        $type = BusinessType::findOrFail($request->id);
        $type->businesses = $type->businesses()->get();
        return response()->json($type);
    }

    public function getPosts(Request $request) {
        $request->validate([
            'id' => 'required|integer'
        ]);
        $type = BusinessType::findOrFail($request->id);
        $posts = $type->posts()->get();
        return response()->json($posts);
    }
}
