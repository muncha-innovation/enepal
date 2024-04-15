<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BusinessType;
use Illuminate\Http\Request;

class BusinessTypesController extends Controller
{
    public function index()
    {
        $types = BusinessType::all();
        $types->map(function($type) {
            $type->businesses = $type->businesses()->limit(10)->get();
        });
        return response()->json($types);
    }
}
