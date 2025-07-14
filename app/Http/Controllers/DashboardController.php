<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $user = auth()->user();
        
        // Get user's businesses using the many-to-many relationship
        $businesses = Business::whereHas('users', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['posts' => function($query) {
            $query->withCount(['comments', 'likes']);
        }])->get();
        
        // Dashboard statistics
        $stats = [
            'total_businesses' => $businesses->count(),
            'total_posts' => $businesses->sum(function($business) {
                return $business->posts->count();
            }),
            'total_comments' => 0,
            'pending_comments' => 0,
        ];
        
        // Get business IDs for efficient querying
        $businessIds = $businesses->pluck('id')->toArray();
        
        // Calculate comment statistics across all businesses
        if (!empty($businessIds)) {
            $stats['total_comments'] = Comment::whereHas('post', function($query) use ($businessIds) {
                $query->whereIn('business_id', $businessIds);
            })->count();
            
            $stats['pending_comments'] = Comment::whereHas('post', function($query) use ($businessIds) {
                $query->whereIn('business_id', $businessIds);
            })->pending()->count();
        }
        
        // Recent activity data
        $recentData = [
            'recent_posts' => !empty($businessIds) ? Post::whereIn('business_id', $businessIds)
                ->with(['business', 'user'])
                ->withCount(['comments', 'likes'])
                ->latest()
                ->limit(5)
                ->get() : collect(),
            'recent_comments' => !empty($businessIds) ? Comment::whereHas('post', function($query) use ($businessIds) {
                $query->whereIn('business_id', $businessIds);
            })
                ->with(['user', 'post.business'])
                ->latest('comments.created_at')
                ->limit(8)
                ->get() : collect(),
            'pending_comments' => !empty($businessIds) ? Comment::whereHas('post', function($query) use ($businessIds) {
                $query->whereIn('business_id', $businessIds);
            })
                ->pending()
                ->with(['user', 'post.business'])
                ->latest('comments.created_at')
                ->limit(5)
                ->get() : collect(),
        ];
        
        return view('dashboard', compact('businesses', 'stats', 'recentData'));
    }
}
