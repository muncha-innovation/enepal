<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Http\Requests\StoreLanguageRequest;

class BusinessLanguageController extends Controller
{
    public function index()
    {
        $languages = Language::latest()->paginate(10);
        return view('admin.languages.index', compact('languages'));
    }

    public function create()
    {
        return view('admin.languages.createOrEdit');
    }

    public function store(StoreLanguageRequest $request)
    {
        Language::create($request->validated());

        return redirect()
            ->route('admin.languages.index')
            ->with('success', 'Language created successfully.');
    }

    public function edit(Language $Language)
    {
        return view('admin.languages.createOrEdit', ['language' => $Language]);
    }

    public function update(StoreLanguageRequest $request, Language $language)
    {
        $language->update($request->validated());

        return redirect()
            ->route('admin.languages.index')
            ->with('success', 'Language updated successfully.');
    }

    public function destroy(Language $Language)
    {
        $Language->delete();

        return redirect()
            ->route('admin.languages.index')
            ->with('success', 'Language deleted successfully.');
    }
}
