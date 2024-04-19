<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessTypesResource;
use App\Models\BusinessType;
use Illuminate\Http\Request;

class BusinessTypesController extends Controller
{
    public function index()
    {
        return BusinessTypesResource::collection(BusinessType::all());
    }
}    
