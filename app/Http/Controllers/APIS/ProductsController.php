<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request) {

        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $limit;
        $query = Product::query();

        if ($request->has('businessTypeId')) {
            $query->select('products.*')
                ->join('businesses', 'products.business_id', '=', 'businesses.id')
                ->where('businesses.type_id', $request->businessTypeId);
        }

        $products = $query->when($request->has('businessId'), function ($query) use ($request) {
            return $query->where('products.business_id', $request->businessId);
        })
            ->when($request->has('userId'), function ($query) use ($request) {
                return $query->where('products.user_id', $request->userId);
            })

            ->latest()
            ->offset($offset)->limit($limit)->get();

        return ProductResource::collection($products);
    }

    public function getById(Request $request, $id)
    {
        return new ProductResource(Product::with(['user', 'user.addresses', 'business', 'business.address'])->findOrFail($id));
    }
}
