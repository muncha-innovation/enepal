<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessResource;
use App\Http\Resources\NewsResource;
use App\Http\Resources\PostResource;
use App\Services\SearchService;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        try {
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10);
            
            $results = $this->searchService->search($request, $page, $perPage);
            
            return response()->json([
                'status' => true,
                'data' => [
                    'posts' => [
                        'data' => PostResource::collection($results['posts']),
                        'meta' => [
                            'current_page' => $results['posts']->currentPage(),
                            'last_page' => $results['posts']->lastPage(),
                            'per_page' => $results['posts']->perPage(),
                            'total' => $results['posts']->total(),
                        ]
                    ],
                    'businesses' => [
                        'data' => BusinessResource::collection($results['businesses']),
                        'meta' => [
                            'current_page' => $results['businesses']->currentPage(),
                            'last_page' => $results['businesses']->lastPage(),
                            'per_page' => $results['businesses']->perPage(),
                            'total' => $results['businesses']->total(),
                        ]
                    ],
                    'localNews' => [
                        'data' => NewsResource::collection($results['localNews']),
                        'meta' => [
                            'current_page' => $results['localNews']->currentPage(),
                            'last_page' => $results['localNews']->lastPage(),
                            'per_page' => $results['localNews']->perPage(),
                            'total' => $results['localNews']->total(),
                        ]
                    ],
                    'nepalNews' => [
                        'data' => NewsResource::collection($results['nepalNews']),
                        'meta' => [
                            'current_page' => $results['nepalNews']->currentPage(),
                            'last_page' => $results['nepalNews']->lastPage(),
                            'per_page' => $results['nepalNews']->perPage(),
                            'total' => $results['nepalNews']->total(),
                        ]
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error while searching: ' . $e->getMessage()
            ], 500);
        }
    }

    public function searchPosts(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:50',
            'filter' => 'nullable|string|in:all,latest,trending',
        ]);

        try {
            $results = $this->searchService->searchPosts(
                $request->get('query'),
                $request->get('filter', 'all'),
                $request->input('page', 1),
                $request->input('per_page', 10)
            );

            return response()->json([
                'status' => true,
                'data' => PostResource::collection($results),
                'meta' => [
                    'current_page' => $results->currentPage(),
                    'last_page' => $results->lastPage(),
                    'per_page' => $results->perPage(),
                    'total' => $results->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error while searching posts: ' . $e->getMessage()
            ], 500);
        }
    }

    public function searchNews(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:50',
            'locality' => 'nullable|string|in:all,local,nepal',
            'filter' => 'nullable|string|in:forYou,latest,trending',
        ]);

        try {
            $point = $this->getPointFromRequest($request);
            
            $results = $this->searchService->searchNews(
                $request->get('query'),
                $request->get('locality', 'all'),
                $request->get('filter', 'forYou'),
                $point,
                auth()->user(),
                $request->input('page', 1),
                $request->input('per_page', 10)
            );

            return response()->json([
                'status' => true,
                'data' => NewsResource::collection($results),
                'meta' => [
                    'current_page' => $results->currentPage(),
                    'last_page' => $results->lastPage(),
                    'per_page' => $results->perPage(),
                    'total' => $results->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error while searching news: ' . $e->getMessage()
            ], 500);
        }
    }

    public function searchBusinesses(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:50',
            'filter' => 'nullable|string|in:all,nearYou,popular',
        ]);

        try {
            $point = $this->getPointFromRequest($request);
            
            $results = $this->searchService->searchBusinesses(
                $request->get('query'),
                $request->get('filter', 'all'),
                $point,
                $request->input('page', 1),
                $request->input('per_page', 10)
            );

            return response()->json([
                'status' => true,
                'data' => BusinessResource::collection($results),
                'meta' => [
                    'current_page' => $results->currentPage(),
                    'last_page' => $results->lastPage(),
                    'per_page' => $results->perPage(),
                    'total' => $results->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error while searching businesses: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getPointFromRequest($request): ?Point
    {
        $lat = $request->header('Latitude');
        $lng = $request->header('Longitude');
        
        return ($lat && $lng) ? new Point($lat, $lng) : null;
    }
}
