<?php

namespace App\Http\Controllers;

use App\Models\NewsItem;
use App\Models\NewsCategory;
use Illuminate\Http\Request;

class NewsCurationController extends Controller
{
    public function index()
    {
        $uncuratedNews = NewsItem::where('is_curated', false)
            ->latest('published_at')
            ->paginate(20);
            
        $categories = NewsCategory::get()->groupBy('type');
        
        return view('modules.news.curation.index', compact('uncuratedNews', 'categories'));
    }

    public function curate(Request $request, NewsItem $newsItem)
    {
        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'exists:news_categories,id',
            'parent_news_id' => 'nullable|exists:news_items,id'
        ]);

        $newsItem->categories()->sync($validated['categories']);
        
        if (!empty($validated['parent_news_id'])) {
            $parentNews = NewsItem::find($validated['parent_news_id']);
            $parentNews->childNews()->attach($newsItem->id);
        }
        
        $newsItem->is_curated = true;
        $newsItem->save();

        return redirect()->back()->with('success', 'News curated successfully');
    }
} 