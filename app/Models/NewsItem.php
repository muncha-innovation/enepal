<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsItem extends Model
{
    protected $fillable = [
        'title',
        'description',
        'url',
        'image',
        'published_at',
        'id',
        'original_id',
        'is_active',
        'is_rejected',
        'is_featured',
        'sourceable_id',
        'sourceable_type',
        'created_by',
        'language',

    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function parentNews()
    {
        return $this->belongsToMany(NewsItem::class, 'news_relationships', 'child_news_id', 'parent_news_id')
            ->withTimestamps();
    }

    public function childNews()
    {
        return $this->belongsToMany(NewsItem::class, 'news_relationships', 'parent_news_id', 'child_news_id')
            ->withTimestamps();
    }

    public function isMainNews()
    {
        return $this->childNews()->exists();
    }

    public function isSubNews()
    {
        return $this->parentNews()->exists();
    }

    public function sourceable()
    {
        return $this->morphTo();
    }
    public function categories()
    {
        return $this->belongsToMany(NewsCategory::class, 'news_item_category');
    }

    public function locations()
    {
        return $this->hasMany(NewsLocation::class);
    }

    public function tags()
    {
        return $this->belongsToMany(NewsTag::class, 'news_item_tag');
    }

    public function genders()
    {
        return $this->belongsToMany(UserGender::class, 'news_item_gender', 'news_item_id', 'gender_id');
    }

    public function ageGroups()
    {
        return $this->belongsToMany(AgeGroup::class, 'news_item_age_group');
    }

    public function getFormattedLocationsAttribute()
    {
        $locations = $this->locations->map(function($location) {
            return [
                'id' => $location->place_id ?: $location->name,
                'text' => $location->name,
                'selected' => true
            ];
        })->toArray();
        return $locations;
    }

    public function scopeInCountry($query, $countryId)
    {
        return $query->whereHas('locations', function ($q) use ($countryId) {
            $q->where('country_id', $countryId);
        });
    }

    public function scopeInState($query, $stateId)
    {
        return $query->whereHas('locations', function ($q) use ($stateId) {
            $q->where('state_id', $stateId);
        });
    }

    public function scopeNepalNews($query)
    {
        return $query->whereHas('locations', function ($q) {
            $q->whereHas('country', function ($query) {
                $query->where('code', 'np');
            });
        });
    }

    public function scopeByUserPreferences($query, $user)
    {
        if (!$user || !$user->preference) {
            return $query;
        }

        $preference = $user->preference;

        // Filter by user's preferred categories if they exist
        if ($user->preferredCategories()->exists()) {
            $query->whereHas('categories', function ($q) use ($user) {
                $q->whereIn('news_categories.id', $user->preferredCategories()->pluck('id'));
            });
        }

        // Apply language preference if set
        if ($preference->app_language) {
            $query->where('language', $preference->app_language);
        }

        // Apply location preferences if set
        if (is_array($preference->countries) && !empty($preference->countries)) {
            $query->whereHas('locations', function ($q) use ($preference) {
                $q->whereIn('country_id', $preference->countries);
            });
        }

        // Apply user type specific filters
        switch ($preference->user_type) {
            case 'student':
                $query->where(function ($q) use ($preference) {
                    $q->whereHas('categories', function ($catQ) {
                        $catQ->where('type', 'education');
                    })->orWhereHas('tags', function ($tagQ) use ($preference) {
                        if ($preference->study_field) {
                            $tagQ->where('name', 'LIKE', "%{$preference->study_field}%");
                        }
                        if ($preference->education_level) {
                            $tagQ->orWhere('name', 'LIKE', "%{$preference->education_level}%");
                        }
                    });
                });
                break;

            case 'job_seeker':
                $query->where(function ($q) use ($preference) {
                    $q->whereHas('categories', function ($catQ) {
                        $catQ->where('type', 'jobs');
                    });
                    
                    if (!empty($preference->job_sectors)) {
                        $q->orWhereHas('tags', function ($tagQ) use ($preference) {
                            $tagQ->whereIn('name', $preference->job_sectors);
                        });
                    }
                });
                break;

            case 'nrn':
                if ($user->primaryAddress) {
                    $query->whereHas('locations', function ($q) use ($user) {
                        $q->where('country_id', $user->primaryAddress->country_id)
                            ->orWhereHas('country', function ($countryQ) {
                                $countryQ->where('code', 'np');
                            });
                    });
                }
                break;
        }

        return $query;
    }

    public function scopeRecommended($query, $user)
    {
        return $query->byUserPreferences($user)
            ->where('is_active', true)
            ->orderBy('published_at', 'desc');
    }

    public function scopeByLocationType($query, $type)
    {
        switch ($type) {
            case 'local':
                if (auth()->check() && auth()->user()->primaryAddress) {
                    $address = auth()->user()->primaryAddress;
                    $query->whereHas('locations', function ($q) use ($address) {
                        $q->where('country_id', $address->country_id)
                            ->orWhere('state_id', $address->state_id);
                    });
                }
                break;
                
            case 'nepal':
                $query->whereHas('locations', function ($q) {
                    $q->whereHas('country', function ($countryQ) {
                        $countryQ->where('code', 'np');
                    });
                });
                break;
        }
        
        return $query;
    }

    public function scopeMatchingInterests($query, $interests)
    {
        if (empty($interests)) {
            return $query;
        }

        return $query->where(function ($q) use ($interests) {
            $q->whereHas('tags', function ($tagQ) use ($interests) {
                $tagQ->whereIn('name', $interests);
            })->orWhereHas('categories', function ($catQ) use ($interests) {
                $catQ->whereIn('name', $interests);
            });
        });
    }

    public function scopeOrderByDistance($query, $point = null)
    {
        if (!$point && auth()->check() && auth()->user()->primaryAddress?->location) {
            $point = auth()->user()->primaryAddress->location;
        }

        if ($point) {
            $query->leftJoin('news_locations', 'news_items.id', '=', 'news_locations.news_item_id')
                ->select('news_items.*')
                ->selectRaw('MIN(ST_Distance_Sphere(news_locations.location, ST_GeomFromText(?))) as distance', [
                    sprintf('POINT(%f %f)', $point->getLng(), $point->getLat())
                ])
                ->groupBy('news_items.id')
                ->orderBy('distance', 'asc');
        }

        return $query;
    }

    public function scopeWithinRadius($query, $radius, $point = null)
    {
        if (!$point && auth()->check() && auth()->user()->primaryAddress?->location) {
            $point = auth()->user()->primaryAddress->location;
        }

        if ($point) {
            $query->whereHas('locations', function ($q) use ($point, $radius) {
                $q->whereRaw('ST_Distance_Sphere(location, ST_GeomFromText(?)) <= ?', [
                    sprintf('POINT(%f %f)', $point->getLng(), $point->getLat()),
                    $radius * 1000 // Convert km to meters
                ]);
            });
        }

        return $query;
    }
}
