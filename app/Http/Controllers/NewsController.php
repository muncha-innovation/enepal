<?php

namespace App\Http\Controllers;

use App\Models\NewsItem;
use App\Models\NewsCategory;
use App\Models\NewsSource;
use App\Models\NewsTag;
use App\Models\UserGender;
use Illuminate\Http\Request;
use App\Http\Requests\StoreNewsRequest;
use App\Models\AgeGroup;
use Illuminate\Support\Facades\Artisan;

class NewsController extends Controller
{
    public function index()
    {
        $sources = NewsSource::where('is_active', true)->get();
        
        $news = NewsItem::with(['sourceable', 'categories'])
            ->when(request('search'), function($query) {
                $query->where('title', 'like', '%' . request('search') . '%');
            })
            ->when(request('source'), function($query) {
                $query->where('sourceable_id', request('source'));
            })
            ->when(request('status'), function($query, $status) {
                if ($status === 'active') {
                    $query->where('is_active', true)->where('is_rejected', false);
                } elseif ($status === 'rejected') {
                    $query->where('is_rejected', true);
                }
            })
            ->latest('published_at')
            ->paginate(20)
            ->withQueryString();
            
        return view('modules.news.index', compact('news', 'sources'));
    }

    public function create()
    {
        $sources = NewsSource::where('is_active', true)->get();
        $categories = NewsCategory::orderBy('name')->get()->groupBy('type');
        $genders = UserGender::all();
        $ageGroups = AgeGroup::all();
        return view('modules.news.create', compact('sources', 'categories', 'genders', 'ageGroups'));
    }

    public function store(StoreNewsRequest $request)
    {
        $validated = $request->validated();
        
        
        $newsData = collect($validated)->except(['locations', 'categories', 'tags', 'age_groups'])->toArray();
        $newsData['published_at'] = now();
        $newsData['sourceable_id'] = auth()->id();
        $newsData['sourceable_type'] = 'App\Models\User';
        $news = NewsItem::create($newsData);

        if (isset($validated['categories'])) {
            $news->categories()->sync($validated['categories']);
        }

        if (isset($validated['tags'])) {
            foreach ($validated['tags'] as $tagName) {
                $tag = NewsTag::firstOrCreate(
                    ['name' => $tagName],
                    ['usage_count' => 0]
                );
                $tag->increment('usage_count');
            }
            
            $tagIds = NewsTag::whereIn('name', $validated['tags'])->pluck('id');
            $news->tags()->sync($tagIds);
        }

        if (isset($validated['locations'])) {
            foreach ($validated['locations'] as $locationData) {
                $news->locations()->create([
                    'name' => $locationData['name'],
                    'latitude' => $locationData['latitude'],
                    'longitude' => $locationData['longitude'],
                    'radius' => $locationData['radius']
                ]);
            }
        }

        // Sync age groups
        if ($request->has('age_groups')) {
            $news->ageGroups()->sync($request->age_groups);
        }

        return redirect()->route('admin.news.index')->with('success', 'News created successfully');
    }

    public function edit(NewsItem $news)
    {
        $sources = NewsSource::where('is_active', true)->get();
        $categories = NewsCategory::get()->groupBy('type');
        $genders = UserGender::all();
        $ageGroups = AgeGroup::all();

        return view('modules.news.edit', compact('news', 'sources', 'categories', 'genders', 'ageGroups'));
    }

    public function update(StoreNewsRequest $request, NewsItem $news)
    {
        $validated = $request->validated();
        
        // Remove locations and categories from the data going into news_items table
        $newsData = collect($validated)->except(['locations', 'categories', 'tags', 'age_groups'])->toArray();
        
        $news->update($newsData);

        if (isset($validated['categories'])) {
            $news->categories()->sync($validated['categories']);
        }

        if (isset($validated['tags'])) {
            foreach ($validated['tags'] as $tagName) {
                $tag = NewsTag::firstOrCreate(
                    ['name' => $tagName],
                    ['usage_count' => 0]
                );
                $tag->increment('usage_count');
            }
            
            $tagIds = NewsTag::whereIn('name', $validated['tags'])->pluck('id');
            $news->tags()->sync($tagIds);
        }

        // Handle locations
        if (isset($validated['locations'])) {
            // Delete existing locations
            $news->locations()->delete();
            
            // Create new locations
            foreach ($validated['locations'] as $locationData) {
                $news->locations()->create([
                    'name' => $locationData['name'],
                    'latitude' => $locationData['latitude'],
                    'longitude' => $locationData['longitude'],
                    'radius' => $locationData['radius']
                ]);
            }
        }

        // Sync genders
        $news->genders()->sync($request->input('genders', []));

        // Sync age groups
        $news->ageGroups()->sync($request->input('age_groups', []));

        return back()->with('success', 'News updated successfully');
    }

    public function destroy(NewsItem $news)
    {
        $currentPage = request()->get('page', 1);
        $news->delete();
        
        return redirect()
            ->route('admin.news.index', ['page' => $currentPage])
            ->with('success', 'News deleted successfully');
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



    public function manageRelated(NewsItem $news)
{
    $subNews = $news->childNews()
        ->when(request('search'), function($query) {
            $query->where('news_items.title', 'like', '%' . request('search') . '%');
        })
        ->latest()
        ->paginate(10);

    $availableNews = NewsItem::where('news_items.id', '!=', $news->id)
        ->when(request('available_search'), function($query) {
            $query->where('news_items.title', 'like', '%' . request('available_search') . '%');
        })
        ->latest()
        ->paginate(10);

    $categories = NewsCategory::orderBy('name')->get()->groupBy('type');
    $genders = UserGender::all();
    $ageGroups = AgeGroup::all();
    return view('modules.news.manage-related', compact('news', 'subNews', 'availableNews', 'categories','genders', 'ageGroups'));
}

    public function addRelated(NewsItem $news, NewsItem $related)
    {
        \DB::transaction(function() use ($news, $related) {
            // Detach the related news from its current parents
            $related->parentNews()->detach();

            // Attach the related news as sub-news without removing existing child news
            $news->childNews()->syncWithoutDetaching($related->id);
        });

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


    public function searchTags(Request $request)
    {
        $search = $request->get('q');
        $tags = NewsTag::where('name', 'like', "%{$search}%")
            ->orderBy('usage_count', 'desc')
            ->limit(10)
            ->get();
        return response()->json($tags);
    }

    public function updateTags(Request $request, NewsItem $news)
    {
        $validated = $request->validate([
            'tags' => 'required|array',
            'tags.*' => 'string|max:50'
        ]);

        foreach ($validated['tags'] as $tagName) {
            $tag = NewsTag::firstOrCreate(
                ['name' => $tagName],
                ['usage_count' => 0]
            );
            $tag->increment('usage_count');
        }

        $tagIds = NewsTag::whereIn('name', $validated['tags'])->pluck('id');
        $news->tags()->sync($tagIds);

        return response()->json(['success' => true]);
    }

    public function fetch(Request $request)
    {
        try {
            // Call the artisan command
            Artisan::call('fetch:news');
            
            return redirect()
                ->route('admin.news.index')
                ->with('success', 'News fetch initiated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.news.index')
                ->with('error', 'Failed to fetch news: ' . $e->getMessage());
        }
    }

    public function reject(NewsItem $news)
    {
        $news->update([
            'is_rejected' => true,
            'is_active' => false
        ]);

        return back()->with('success', 'News has been rejected successfully');
    }


    public function activate(NewsItem $news)
    {
        $news->update([
            'is_rejected' => false,
            'is_active' => true
        ]);

        return back()->with('success', 'News has been activated successfully');
    }
}
