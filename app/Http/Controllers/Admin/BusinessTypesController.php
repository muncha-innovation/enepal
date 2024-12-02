<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBusinessTypeRequest;
use App\Models\BusinessType;
use App\Models\Facility;
use App\Services\DocumentService;
use Illuminate\Http\Request;

class BusinessTypesController extends Controller
{
    public function index() {
        $businessTypes = BusinessType::paginate(10);
        return view('admin-views.businessTypes.index', compact('businessTypes'));
    }

    public function create() {
        $facilities = Facility::all();
        return view('admin-views.businessTypes.createOrEdit', compact('facilities'));
    }
    public function edit(Request $request, BusinessType $businessType) {
        $facilities = Facility::all();
        return view('admin-views.businessTypes.createOrEdit', compact('facilities','businessType'));
    }
    public function show(BusinessType $businessType) {
        return view('admin-views.businessTypes.show', compact('businessType'));
    }
    public function store(StoreBusinessTypeRequest $request) {
        $data = collect($request->validated())->except('facilities')->toArray();
        
        if($request->hasFile('icon')) {
            $data['icon'] = (new DocumentService())->store($request->file('icon'), 'businessTypes');
        }
        $businessType = BusinessType::create($data);
        $businessType->facilities()->sync($request->facilities);
        return redirect()->route('admin.businessTypes.index')->with('success', 'Business Type created successfully');
    }
    public function update(StoreBusinessTypeRequest $request, BusinessType $businessType) {
        $data = collect($request->validated())->except('facilities')->toArray();
        if($request->hasFile('icon')) {
            $data['icon'] = (new DocumentService())->store($request->file('icon'), 'businessTypes');
        }
        $businessType->update($data);
    $businessType->facilities()->sync($request->facilities);
        
        return redirect()->route('admin.businessTypes.index')->with('success', 'Business Type updated successfully');
    }

    public function destroy() {

    }
}
