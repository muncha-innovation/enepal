<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessTypesResource;
use App\Models\Business;
use App\Models\BusinessType;
use Illuminate\Http\Request;

class BusinessTypesController extends Controller
{
    public function index()
    {
        return BusinessTypesResource::collection(BusinessType::all());
    }

    public function getById($id)
    {
        $type = BusinessType::findOrFail($id);
        return BusinessTypesResource::make($type);
    }
}    
