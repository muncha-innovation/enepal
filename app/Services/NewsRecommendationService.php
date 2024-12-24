<?php

namespace App\Services;

use App\Models\NewsItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class NewsRecommendationService
{
    private $user;
    private $currentLat;
    private $currentLng;

    public function __construct(?User $user = null, ?float $lat = null, ?float $lng = null)
    {
        $this->user = $user;
        $this->currentLat = $lat;
        $this->currentLng = $lng;
    }

    public function getRecommendations(int $limit = 20)
    {
        $query = NewsItem::with(['categories', 'tags', 'source', 'locations','childNews'])
            ->where('is_active', true)
            ->latest('published_at');
        
        // Apply location-based filtering if coordinates are provided
        if ($this->currentLat && $this->currentLng) {
            $query->whereHas('locations', function (Builder $query) {
                $query->whereRaw(
                    '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= radius',
                    [$this->currentLat, $this->currentLng, $this->currentLat]
                );
            });
        }

        // Apply user preferences if user is authenticated
        if ($this->user) {
            // Get user's preferred categories
            $preferredCategories = $this->user->newsPreferences()->pluck('category_id');
            
            // Get user's addresses
            $addresses = $this->user->addresses;
            
            $query->where(function ($query) use ($preferredCategories, $addresses) {
                // Match preferred categories
                if ($preferredCategories->isNotEmpty()) {
                    $query->orWhereHas('categories', function ($q) use ($preferredCategories) {
                        $q->whereIn('id', $preferredCategories);
                    });
                }
                
                // Match locations near user's addresses
                foreach ($addresses as $address) {
                    if ($address->latitude && $address->longitude) {
                        $query->orWhereHas('locations', function ($q) use ($address) {
                            $q->whereRaw(
                                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= radius',
                                [$address->latitude, $address->longitude, $address->latitude]
                            );
                        });
                    }
                }
            });
        }

        // Include trending tags
        $trendingTagIds = DB::table('news_tags')
            ->orderBy('usage_count', 'desc')
            ->limit(10)
            ->pluck('id');

        $query->addSelect(['trending_score' => function ($query) use ($trendingTagIds) {
            $query->selectRaw('COUNT(DISTINCT news_item_tag.news_tag_id)')
                ->from('news_item_tag')
                ->whereIn('news_item_tag.news_tag_id', $trendingTagIds)
                ->whereColumn('news_item_tag.news_item_id', 'news_items.id');
        }]);

        return $query->orderByDesc('trending_score')
                    ->orderByDesc('published_at')
                    ->limit($limit)
                    ->get();
    }
} 