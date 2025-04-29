<?php

namespace App\Services;

use App\Models\Business;
use App\Models\NewsItem;
use App\Models\Post;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use DB;

class SearchService
{
    public function search($request, $page = 1, $perPage = 10)
    {
        $lat = (float) $request->header('Latitude');
        $lng = (float) $request->header('Longitude');
        $point = ($lat && $lng) ? new Point($lat, $lng) : null;
        $user = auth()->user();
        $keyword = $request->get('query');
        $language = $request->get('language', $request->get('lang', 'en')); // Get language from request
        
        return [
            'posts' => $this->searchPosts($keyword, 'all', $page, $perPage),
            'businesses' => $this->searchBusinesses($keyword, 'all', $point, $page, $perPage),
            'localNews' => $this->searchNews($keyword, 'local', 'forYou', $point, $user, $page, $perPage, $language),
            'nepalNews' => $this->searchNews($keyword, 'nepal', 'latest', $point, $user, $page, $perPage, $language)
        ];
    }

    public function searchPosts($keyword, $filter = 'all', $page = 1, $perPage = 10)
    {
        $query = Post::query()
            ->where('is_active', true) // Only active posts
            ->where(function($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%")
                    ->orWhere('short_description', 'like', "%{$keyword}%");
            });

        switch ($filter) {
            case 'trending':
                $query->withCount(['likes', 'comments'])
                    ->orderBy('likes_count', 'desc')
                    ->orderBy('comments_count', 'desc');
                break;
            case 'latest':
                $query->latest();
                break;
            default:
                $query->latest();
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function searchBusinesses($keyword, $filter = 'all', $point = null, $page = 1, $perPage = 10)
    {
        $query = Business::query()
            ->where('businesses.is_active', true)
            ->join('addresses', function($join) {
                $join->on('addresses.addressable_id', '=', 'businesses.id')
                     ->where('addresses.addressable_type', '=', 'App\Models\Business');
            });
            
        if ($point) {
            $query->select([
                'businesses.*',
                DB::raw('ST_Distance_Sphere(addresses.location, ST_GeomFromText(?)) as distance')
            ])
            ->addBinding("POINT({$point->getLng()} {$point->getLat()})", 'select');
        } else {
            $query->select('businesses.*');
        }
        
        $query->where(function($q) use ($keyword) {
                $q->where('businesses.name', 'like', "%{$keyword}%")
                  ->orWhere('businesses.description', 'like', "%{$keyword}%");
            });
            
        if ($point) {
            switch ($filter) {
                case 'nearYou':
                    $query->whereNotNull('addresses.location')
                        ->orderBy('distance', 'asc');
                    break;
                case 'popular':
                    $query->withCount('users')
                        ->orderBy('users_count', 'desc');
                    $query->whereNotNull('addresses.location')
                        ->orderBy('distance', 'asc');
                    break;
                default:
                    $query->whereNotNull('addresses.location')
                        ->orderBy('distance', 'asc')
                        ->latest('businesses.created_at');
            }
        } else {
            $query->latest('businesses.created_at');
        }

        return $query->paginate($perPage);
    }

    public function searchNews($keyword, $locality = 'all', $filter = 'latest', $point = null, $user = null, $page = 1, $perPage = 10, $language = 'en')
    {
        $query = NewsItem::query()
            ->where('news_items.is_active', true)
            ->where('news_items.language', $language);
            
        // Only apply keyword filter if the keyword is provided and not empty
        if (!empty($keyword)) {
            $query->where(function($q) use ($keyword) {
                $q->where('news_items.title', 'like', "%{$keyword}%")
                    ->orWhere('news_items.description', 'like', "%{$keyword}%");
            });
        }
        
        // Select fields based on whether point is available
        if ($point) {
            $query->select([
                'news_items.id',
                'news_items.title',
                'news_items.description',
                'news_items.published_at',
                'news_items.views_count',
                'news_items.sourceable_type',
                'news_items.sourceable_id',
                'news_items.is_active',
                \DB::raw('MIN(ST_Distance_Sphere(news_locations.location, ST_GeomFromText(?))) as distance'),
                'news_items.language',
                'news_items.image',
                'news_items.url',
                'news_items.created_at',
                'news_items.updated_at'
            ])
            ->addBinding("POINT({$point->getLng()} {$point->getLat()})", 'select');
        } else {
            $query->select([
                'news_items.id',
                'news_items.title',
                'news_items.description',
                'news_items.published_at',
                'news_items.views_count',
                'news_items.sourceable_type',
                'news_items.sourceable_id',
                'news_items.is_active',
                'news_items.language',
                'news_items.image',
                'news_items.url',
                'news_items.created_at',
                'news_items.updated_at'
            ]);
        }
        
        // Use left join instead of inner join to include news items without locations
        $query->leftJoin('news_locations', 'news_items.id', '=', 'news_locations.news_item_id')
            ->groupBy([
                'news_items.id',
                'news_items.title',
                'news_items.is_active',
                'news_items.description',
                'news_items.published_at',
                'news_items.views_count',
                'news_items.sourceable_type',
                'news_items.sourceable_id',
                'news_items.language',
                'news_items.image',
                'news_items.url',
                'news_items.created_at',
                'news_items.updated_at'
            ]);
        
        switch ($locality) {
            case 'nepal':
                // Use left join for countries as well
                $query->leftJoin('countries', 'news_locations.country_id', '=', 'countries.id')
                    ->where(function($q) {
                        $q->whereRaw('LOWER(countries.code) = ?', ['np'])
                          ->orWhereNull('news_locations.id'); // Include news without location
                    });
                break;
            case 'local':
                if ($user && $user->primaryAddress) {
                    $query->where(function($q) use ($user) {
                        $q->where('news_locations.country_id', $user->primaryAddress->country_id)
                          ->orWhere('news_locations.state_id', $user->primaryAddress->state_id)
                          ->orWhereNull('news_locations.id'); // Include news without location
                    });
                } elseif ($point) {
                    $query->where(function($q) {
                        $q->whereNotNull('news_locations.location')
                          ->orWhereNull('news_locations.id'); // Include news without location
                    });
                    
                    if ($point) {
                        // Use MIN(news_locations.id) to check for existence of location
                        $query->orderByRaw('CASE WHEN MIN(news_locations.id) IS NULL THEN 1 ELSE 0 END asc') 
                              ->orderBy('distance', 'asc');
                    }
                }
                break;
            default:
                if ($point) {
                    $query->where(function($q) {
                        $q->whereNotNull('news_locations.location')
                          ->orWhereNull('news_locations.id'); // Include news without location
                    });
                    
                    // Use MIN(news_locations.id) to check for existence of location
                    $query->orderByRaw('CASE WHEN MIN(news_locations.id) IS NULL THEN 1 ELSE 0 END asc')
                          ->orderBy('distance', 'asc');
                }
        }

        // Apply content filter
        switch ($filter) {
            case 'forYou':
                if ($user && $user->preferredCategories()->exists()) {
                    $query->join('news_item_category', 'news_items.id', '=', 'news_item_category.news_item_id')
                        ->join('news_preferences', 'news_item_category.news_category_id', '=', 'news_preferences.category_id')
                        ->where('news_preferences.user_id', $user->id);
                } else {
                    $query->orderBy('news_items.published_at', 'desc');
                }
                break;
            case 'trending':
                $query->orderBy('news_items.views_count', 'desc')
                    ->orderBy('news_items.published_at', 'desc');
                break;
            case 'latest':
            default:
                $query->orderBy('news_items.published_at', 'desc');
        }

        // Include childNews relationship in the results
        $results = $query->paginate($perPage);
        
        // Load the childNews relationship for each news item in the collection
        $results->getCollection()->load(['childNews', 'categories', 'tags', 'locations']);
        
        return $results;
    }
}
