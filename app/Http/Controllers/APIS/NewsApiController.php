<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use App\Models\NewsItem;
use App\Models\NewsCategory;
use App\Models\NewsTag;
use App\Http\Resources\NewsResource;
use Illuminate\Support\Facades\Auth;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Facades\Request;

class NewsApiController extends Controller
{

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

    public function primary()
    {
        $query = NewsItem::with(['categories', 'tags', 'sourceable', 'locations','childNews'])
            ->where('is_active', true);

        if (Auth::check()) {
            $user = Auth::user();
            $primaryAddress = $user->primaryAddress;

            if ($primaryAddress) {
                $query->whereHas('locations', function ($q) use ($primaryAddress) {
                    $q->where(function ($subQ) use ($primaryAddress) {
                        $subQ->where('country_id', $primaryAddress->country_id)
                            ->orWhere('state_id', $primaryAddress->state_id);
                    });
                    
                    if ($primaryAddress->location) {
                        $q->orderByDistance('location', new Point(
                            $primaryAddress->location->getLat(),
                            $primaryAddress->location->getLng()
                        ));
                    }
                });
            }
        }

        $news = $query->latest('published_at')->paginate(20);
        return NewsResource::collection($news);
    }

    public function secondary()
    {
        $query = NewsItem::with(['categories', 'tags', 'sourceable', 'locations','childNews'])
            ->where('is_active', true);

        if (Auth::check()) {
            $user = Auth::user();
            $birthAddress = $user->birthAddress;

            if ($birthAddress) {
                $query->whereHas('locations', function ($q) use ($birthAddress) {
                    $q->where('country_id', $birthAddress->country_id);
                    
                    if ($birthAddress->location) {
                        $q->orderByDistance('location', new Point(
                            $birthAddress->location->getLat(),
                            $birthAddress->location->getLng()
                        ));
                    }
                });
            }
        }

        $news = $query->latest('published_at')->paginate(20);
        return NewsResource::collection($news);
    }
}