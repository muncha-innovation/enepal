<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\NewsItem;
use App\Models\NewsCategory;
use App\Models\NewsTag;
use App\Http\Resources\NewsResource;

class NewsApiController extends Controller
{
    public function index()
    {
        $news = NewsItem::with(['categories', 'tags', 'source'])
            ->where('is_active', true)
            ->latest('published_at')
            ->paginate(20);

        return NewsResource::collection($news);
    }

    public function show(NewsItem $newsItem)
    {
        return new NewsResource($newsItem->load(['categories', 'tags', 'source','childNews']));
    }

    public function category(NewsCategory $category)
    {
        $news = $category->newsItems()
            ->with(['categories', 'tags', 'source'])
            ->where('is_active', true)
            ->latest('published_at')
            ->paginate(20);

        return NewsResource::collection($news);
    }

    public function tag(NewsTag $tag)
    {
        $news = $tag->news()
            ->with(['categories', 'tags', 'source'])
            ->where('is_active', true)
            ->latest('published_at')
            ->paginate(20);

        return NewsResource::collection($news);
    }
} 