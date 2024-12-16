<?php

namespace App\Http\Controllers;

use App\Models\NewsSource;
use Illuminate\Http\Request;

class NewsSourceController extends Controller
{
    public function index()
    {
        $sources = NewsSource::latest()->paginate(20);
        return view('modules.news.sources.index', compact('sources'));
    }

    public function create()
    {
        return view('modules.news.sources.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'logo' => 'nullable|url',
            'language' => 'required|string|in:en,np',
            'is_active' => 'boolean'
        ]);

        NewsSource::create($validated);
        return redirect()->route('admin.news-sources.index')->with('success', 'News source created successfully');
    }

    public function edit(NewsSource $newsSource)
    {
        return view('modules.news.sources.edit', compact('newsSource'));
    }

    public function update(Request $request, NewsSource $newsSource)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'logo' => 'nullable|url',
            'language' => 'required|string|in:en,np',
            'is_active' => 'boolean'
        ]);

        $newsSource->update($validated);
        return redirect()->route('admin.news-sources.index')->with('success', 'News source updated successfully');
    }

    public function destroy(NewsSource $newsSource)
    {
        $newsSource->delete();
        return redirect()->route('admin.news-sources.index')->with('success', 'News source deleted successfully');
    }
} 