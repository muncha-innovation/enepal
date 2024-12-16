<?php

namespace App\Http\Controllers;

use App\Models\NewsItem;
use App\Models\NewsCategory;
use App\Models\NewsSource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index()
    {
        $sources = NewsSource::where('is_active', true)->get();
        
        $news = NewsItem::with(['source', 'categories'])
            ->when(request('search'), function($query) {
                $query->where('title', 'like', '%' . request('search') . '%');
            })
            ->when(request('source'), function($query) {
                $query->where('source_id', request('source'));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();
            
        return view('modules.news.index', compact('news', 'sources'));
    }

    public function create()
    {
        $sources = NewsSource::where('is_active', true)->get();
        $categories = NewsCategory::get()->groupBy('type');

        return view('modules.news.create', compact('sources', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'required|string',
            'source_id' => 'required|exists:news_sources,id',
            'categories' => 'required|array',
            'categories.*' => 'exists:news_categories,id',
            'published_at' => 'nullable|date',
            'is_active' => 'boolean'
        ]);

        $news = NewsItem::create($validated);
        $news->categories()->sync($request->categories);

        return redirect()->route('admin.news.index')->with('success', 'News created successfully');
    }

    public function edit(NewsItem $news)
    {
        $sources = NewsSource::where('is_active', true)->get();
        $categories = NewsCategory::get()->groupBy('type');

        return view('modules.news.edit', compact('news', 'sources', 'categories'));
    }

    public function update(Request $request, NewsItem $news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'source_id' => 'required|exists:news_sources,id',
            'categories' => 'sometimes|array',
            'categories.*' => 'exists:news_categories,id',
            'published_at' => 'required|date',
            'is_active' => 'boolean',
            'image' => 'nullable|string'
        ]);

        $news->update($validated);
        $news->categories()->sync($request->categories);

        return redirect()->route('admin.news.index')->with('success', 'News updated successfully');
    }

    public function destroy(NewsItem $news)
    {
        $news->delete();
        return redirect()->route('admin.news.index')->with('success', 'News deleted successfully');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048'
        ]);

        $path = $request->file('image')->store('news', 'public');
        
        return response()->json([
            'url' => asset('storage/' . $path)
        ]);
    }

    // Public methods
    public function publicIndex()
    {
        $news = NewsItem::with(['source', 'categories'])
            ->where('is_active', true)
            ->latest('published_at')
            ->paginate(12);

        return view('modules.frontend.news.index', compact('news'));
    }

    public function publicShow(NewsItem $newsItem)
    {
        $relatedNews = $newsItem->categories()
            ->first()
            ->newsItems()
            ->where('id', '!=', $newsItem->id)
            ->limit(4)
            ->get();

        return view('modules.frontend.news.show', compact('newsItem', 'relatedNews'));
    }

    public function publicCategory(NewsCategory $category)
    {
        $news = $category->newsItems()
            ->where('is_active', true)
            ->latest('published_at')
            ->paginate(12);

        return view('modules.frontend.news.category', compact('category', 'news'));
    }

    public function show(NewsItem $news)
    {
        return view('modules.news.show', compact('news'));
    }

    public function curation()
    {
        $uncuratedNews = NewsItem::with(['source', 'categories'])
            ->latest()
            ->paginate(20);

        $categories = NewsCategory::get()->groupBy('type');

        return view('modules.news.curation.index', compact('uncuratedNews', 'categories'));
    }

    public function curate(Request $request, NewsItem $news)
    {
        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'exists:news_categories,id',
            'main_news_id' => 'nullable|exists:news_items,id'
        ]);

        $news->categories()->sync($request->categories);
        if ($request->main_news_id) {
            $news->main_news_id = $request->main_news_id;
        }
        $news->curated_at = now();
        $news->save();

        return redirect()->back()->with('success', 'News curated successfully');
    }

    public function manageRelated(NewsItem $news)
    {
        $subNews = $news->isMainNews() 
            ? $news->childNews()
                ->when(request('search'), function($query) {
                    $query->where('news_items.title', 'like', '%' . request('search') . '%');
                })
                ->latest()
                ->paginate(10)
            : collect();

        $availableNews = !$news->isSubNews() 
            ? NewsItem::where('news_items.id', '!=', $news->id)
                ->whereDoesntHave('parentNews')
                ->where('news_items.source_id', $news->source_id)
                ->when(request('available_search'), function($query) {
                    $query->where('news_items.title', 'like', '%' . request('available_search') . '%');
                })
                ->latest()
                ->paginate(10)
            : collect();

        return view('modules.news.manage-related', compact('news', 'subNews', 'availableNews'));
    }

    public function addRelated(NewsItem $news, NewsItem $related)
    {
        if ($related->isMainNews()) {
            return back()->with('error', 'Cannot add a main news as sub-news.');
        }

        $news->childNews()->attach($related->id);
        return back()->with('success', 'Related news added successfully');
    }

    public function removeRelated(NewsItem $news, NewsItem $related)
    {
        $news->childNews()->detach($related->id);
        return back()->with('success', 'Related news removed successfully');
    }

    public function promoteToMain(NewsItem $news)
    {
        if (!$news->isSubNews()) {
            return back()->with('error', 'This news is already a main news or has no relations.');
        }

        \DB::transaction(function() use ($news) {
            foreach ($news->parentNews as $parent) {
                // Get all siblings excluding the current news
                $siblings = $parent->childNews()
                    ->where('news_items.id', '!=', $news->id)
                    ->get();
                
                // Make parent a child of the promoted news
                $news->childNews()->attach($parent->id);
                
                // Make siblings children of the promoted news
                $news->childNews()->attach($siblings->pluck('id'));
                
                // Remove old relationships
                $parent->childNews()->detach();
            }
            
            // Remove this news from being a child of any parent
            $news->parentNews()->detach();
        });

        return back()->with('success', 'News promoted to main news successfully');
    }
}
