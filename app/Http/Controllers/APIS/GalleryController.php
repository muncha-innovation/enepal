<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use App\Http\Resources\GalleryResource;
use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Request $request) {

        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $limit;
        $query = Gallery::query();

        if ($request->has('businessTypeId')) {
            $query->select('galleries.*')
                ->join('businesses', 'galleries.business_id', '=', 'businesses.id')
                ->where('businesses.type_id', $request->businessTypeId);
        }

        $galleries = $query->when($request->has('businessId'), function ($query) use ($request) {
            return $query->where('galleries.business_id', $request->businessId);
        })
            ->when($request->has('userId'), function ($query) use ($request) {
                return $query->where('galleries.user_id', $request->userId);
            })

            ->latest()
            ->offset($offset)->limit($limit)->get();

        return GalleryResource::collection($galleries);
    }

    public function getById(Request $request, $id)
    {
        
        return new GalleryResource(Gallery::with(['user', 'user.addresses', 'business', 'business.address'])->findOrFail($id));
    }
}
