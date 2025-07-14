<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Business;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Display recent comments for a business
     */
    public function index(Business $business)
    {
        $comments = $business->comments()
            ->with(['user', 'post', 'replies.user'])
            ->topLevel()
            ->latest('comments.created_at')
            ->paginate(20);

        return view('modules.comments.index', compact('business', 'comments'));
    }

    /**
     * Store a new comment or reply
     */
    public function store(Request $request, Business $business, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|min:1|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if post belongs to business
        if ($post->business_id !== $business->id) {
            return response()->json([
                'success' => false,
                'message' => 'Post does not belong to this business'
            ], 403);
        }

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
            'parent_id' => $request->parent_id,
            'is_approved' => true // Auto-approve for business admins
        ]);

        $comment->load('user', 'replies.user');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'comment' => [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'created_at' => $comment->created_at->diffForHumans(),
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                        'profile_picture' => $comment->user->profile_picture ? asset('storage/' . $comment->user->profile_picture) : null
                    ],
                    'parent_id' => $comment->parent_id,
                    'can_reply' => true
                ]
            ]);
        }

        return back()->with('success', 'Comment added successfully');
    }

  

    /**
     * Approve a comment
     */
    public function approve(Business $business, Comment $comment)
    {
        // Check if comment belongs to business
        if (!$comment->belongsToBusiness($business->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Comment does not belong to this business'
            ], 403);
        }

        $comment->update(['is_approved' => true]);

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Comment approved']);
        }

        return back()->with('success', 'Comment approved successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Business $business, Comment $comment)
    {
        // Check if comment belongs to business or user owns the comment
        if (!$comment->belongsToBusiness($business->id) && $comment->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this comment'
            ], 403);
        }

        // Delete replies first
        $comment->replies()->delete();
        $comment->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Comment deleted']);
        }

        return back()->with('success', 'Comment deleted successfully');
    }

    /**
     * Get comments for a specific post (AJAX)
     */
    public function getPostComments(Business $business, Post $post)
    {
        $comments = $post->comments()
            ->with(['user', 'replies.user'])
            ->topLevel()
            ->approved()
            ->latest()
            ->get();

        $commentsData = $comments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'comment' => $comment->comment,
                'created_at' => $comment->created_at->diffForHumans(),
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                    'profile_picture' => $comment->user->profile_picture ? asset('storage/' . $comment->user->profile_picture) : null
                ],
                'replies' => $comment->replies->map(function ($reply) {
                    return [
                        'id' => $reply->id,
                        'comment' => $reply->comment,
                        'created_at' => $reply->created_at->diffForHumans(),
                        'user' => [
                            'id' => $reply->user->id,
                            'name' => $reply->user->name,
                            'profile_picture' => $reply->user->profile_picture ? asset('storage/' . $reply->user->profile_picture) : null
                        ]
                    ];
                })
            ];
        });

        return response()->json([
            'success' => true,
            'comments' => $commentsData,
            'total' => $comments->count()
        ]);
    }
}
