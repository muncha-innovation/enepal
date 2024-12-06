<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessType;
use App\Models\Facility;
use App\Http\Requests\StoreFacilityRequest;

class FacilitiesController extends Controller
{
    //
    public function index() {
        $facilities = Facility::paginate(10);
        return view('admin.facilities.index', compact('facilities'));
    }
    

    public function create() {
        $businessTypes = BusinessType::all();
        return view('admin.facilities.createOrEdit', compact('businessTypes'));
    }
    public function edit(Request $request, Facility $facility) {
        $businessTypes = BusinessType::all();
        return view('admin.facilities.createOrEdit', compact('businessTypes','facility'));

    }
    public function store(StoreFacilityRequest $request) {
        $data = collect($request->validated())->except('business_types')->toArray();

        $facility = Facility::create($data);
        $facility->businessTypes()->sync($request->business_types);
        return redirect()->route('admin.facilities.index')->with('success', 'Facility created successfully');
    }
    public function update(StoreFacilityRequest $request, Facility $facility) {
        $data = collect($request->validated())->except('business_types')->toArray();
        $facility->update($data);
        $facility->businessTypes()->sync($request->business_types);
        return redirect()->route('admin.facilities.index')->with('success', 'Facility updated successfully');
    }
    public function destroy(Facility $facility) {
        $facility->delete();
        return redirect()->route('admin.facilities.index')->with('success', 'Facility deleted successfully');
    }
}
