<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\UserSegment;
use App\Models\User;
use Illuminate\Http\Request;

class SegmentController extends Controller
{
    /**
     * Store a new segment
     */
    public function store(Request $request, Business $business)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:custom,member,admin'
        ]);

        $segment = $business->segments()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'is_active' => true,
            'is_default' => false
        ]);

        // Check if the request expects JSON or has the X-Requested-With header
        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest' || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Segment created successfully',
                'segment' => $segment
            ]);
        }

        return redirect()->back()->with('success', 'Segment created successfully');
    }

    /**
     * Update an existing segment
     */
    public function update(Request $request, Business $business, UserSegment $segment)
    {
        if ($segment->is_default) {
            return response()->json([
                'success' => false,
                'message' => 'Default segments cannot be modified'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:custom,member,admin'
        ]);

        $segment->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'type' => $validated['type']
        ]);

        // Check if the request expects JSON or has the X-Requested-With header
        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest' || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Segment updated successfully',
                'segment' => $segment
            ]);
        }

        return redirect()->back()->with('success', 'Segment updated successfully');
    }

    /**
     * Delete a segment
     */
    public function destroy(Business $business, UserSegment $segment)
    {
        if ($segment->is_default) {
            return response()->json([
                'success' => false,
                'message' => 'Default segments cannot be deleted'
            ], 403);
        }

        $segment->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Segment deleted successfully'
            ]);
        }

        return redirect()->back()->with('success', 'Segment deleted successfully');
    }

    /**
     * Get all segments for a business
     */
    public function index(Business $business)
    {
        $segments = $business->segments()
            ->with(['users' => function ($query) {
                $query->select('users.id', 'first_name', 'last_name', 'email');
            }])
            ->active()
            ->get();

        return response()->json($segments);
    }

    /**
     * Get preview of users in a segment with pagination
     */
    public function preview(Request $request, Business $business, UserSegment $segment)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 20);
        
        $query = $this->getUsersInSegment($segment);
        
        $total = $query->count();
        $users = $query->skip($offset)->limit($limit)->get();

        return response()->json([
            'users' => $users,
            'count' => $total,
            'offset' => $offset,
            'limit' => $limit
        ]);
    }

    /**
     * Add users to a segment
     */
    public function addUsers(Request $request, Business $business, UserSegment $segment)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        // Only add users that are actually in this business
        $businessUserIds = $business->users()->pluck('users.id')->toArray();
        $usersToAdd = array_intersect($validated['user_ids'], $businessUserIds);

        if (!empty($usersToAdd)) {
            $segment->users()->syncWithoutDetaching($usersToAdd);
        }

        return response()->json([
            'success' => true,
            'message' => 'Users added to segment successfully'
        ]);
    }

    /**
     * Remove users from a segment
     */
    public function removeUsers(Request $request, Business $business, UserSegment $segment)
    {
        if ($segment->is_default) {
            return response()->json([
                'success' => false,
                'message' => 'Users cannot be removed from default segments'
            ], 403);
        }

        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        // Only remove users that are actually in this business
        $businessUserIds = $business->users()->pluck('users.id')->toArray();
        $usersToRemove = array_intersect($validated['user_ids'], $businessUserIds);

        if (!empty($usersToRemove)) {
            $segment->users()->detach($usersToRemove);
        }

        return response()->json([
            'success' => true,
            'message' => 'Users removed from segment successfully'
        ]);
    }

    /**
     * Update a user's segment memberships
     */
    public function updateUserSegments(Request $request, Business $business, User $user)
    {
        $validated = $request->validate([
            'segment_ids' => 'required|array',
            'segment_ids.*' => 'exists:user_segments,id'
        ]);

        // Ensure we only work with segments belonging to this business
        $businessSegments = $business->segments()->pluck('id')->toArray();
        $validSegmentIds = array_intersect($validated['segment_ids'], $businessSegments);
        
        // Default segments that should always be included based on user role
        $userRole = $business->users()->where('users.id', $user->id)->first()->pivot->role ?? 'member';
        $defaultSegment = $business->segments()
            ->where('type', $userRole)
            ->where('is_default', true)
            ->first();
            
        if ($defaultSegment && !in_array($defaultSegment->id, $validSegmentIds)) {
            $validSegmentIds[] = $defaultSegment->id;
        }
        
        // Get current segments for this user within this business
        $currentSegments = $user->segments()
            ->where('business_id', $business->id)
            ->pluck('user_segments.id')
            ->toArray();
        
        // Segments to add
        $segmentsToAdd = array_diff($validSegmentIds, $currentSegments);
        if (!empty($segmentsToAdd)) {
            $user->segments()->syncWithoutDetaching($segmentsToAdd);
        }
        
        // Segments to remove (except default segments)
        $defaultSegmentIds = $business->segments()
            ->where('is_default', true)
            ->pluck('id')
            ->toArray();
            
        $segmentsToRemove = array_diff(
            $currentSegments, 
            $validSegmentIds,
            $defaultSegmentIds // Don't remove default segments
        );
        
        if (!empty($segmentsToRemove)) {
            $user->segments()->detach($segmentsToRemove);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'User segments updated successfully',
            'segments' => $user->segments()->where('business_id', $business->id)->get()
        ]);
    }

    /**
     * Get users in a segment
     */
    private function getUsersInSegment(UserSegment $segment)
    {
        return $segment->users()
            ->with(['preference'])
            ->select('users.*')
            ->distinct();
    }
}
