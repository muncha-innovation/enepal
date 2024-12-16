<?php

namespace App\Http\Controllers;

use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsCategoryController extends Controller
{
    public function index()
    {
        $categories = NewsCategory::with('parent')->latest()->paginate(20);
        return view('modules.news.categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = NewsCategory::get();
        return view('modules.news.categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:geography,category,tags',
            'parent_id' => 'nullable|exists:news_categories,id'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        NewsCategory::create($validated);
        return redirect()->route('admin.news-categories.index')->with('success', 'Category created successfully');
    }

    public function edit(NewsCategory $newsCategory)
    {
        $categories = NewsCategory::where('id', '!=', $newsCategory->id)->get();
        return view('modules.news.categories.edit', compact('newsCategory', 'categories'));
    }

    public function update(Request $request, NewsCategory $newsCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:geography,category,tags',
            'parent_id' => 'nullable|exists:news_categories,id'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        $newsCategory->update($validated);
        return redirect()->route('admin.news-categories.index')->with('success', 'Category updated successfully');
    }

    public function destroy(NewsCategory $newsCategory)
    {
        $newsCategory->delete();
        return redirect()->route('admin.news-categories.index')->with('success', 'Category deleted successfully');
    }
} 