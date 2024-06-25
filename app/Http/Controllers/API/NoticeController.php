<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\NoticeResource;
use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function index(Request $request) {

        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $limit;
        $query = Notice::query();

        if ($request->has('businessTypeId')) {
            $query->select('notices.*')
                ->join('businesses', 'notices.business_id', '=', 'businesses.id')
                ->where('businesses.type_id', $request->businessTypeId);
        }

        $notices = $query->when($request->has('businessId'), function ($query) use ($request) {
            return $query->where('notices.business_id', $request->businessId);
        })
            ->when($request->has('userId'), function ($query) use ($request) {
                return $query->where('notices.user_id', $request->userId);
            })

            ->latest()
            ->offset($offset)->limit($limit)->get();

        return NoticeResource::collection($notices);
    }

    public function getById(Request $request, $id)
    {
        
        return new NoticeResource(Notice::with(['user', 'user.address', 'business', 'business.address'])->findOrFail($id));
    }

}
