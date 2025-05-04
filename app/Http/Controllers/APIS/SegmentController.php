<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\UserSegment;
use Illuminate\Http\Request;

class SegmentController extends Controller
{
    public function index(Business $business)
    {
        $segments = $business->segments()->active()->get();
        return response()->json(['segments' => $segments]);
    }

    public function store(Request $request, Business $business)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:admin,member,custom'
        ]);

        $segment = $business->segments()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'is_active' => true,
            'is_default' => false
        ]);

        return response()->json([
            'message' => 'Segment created successfully',
            'segment' => $segment
        ]);
    }

    public function update(Request $request, Business $business, UserSegment $segment)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:admin,member,custom',
            'is_active' => 'boolean'
        ]);

        if ($segment->is_default && $validated['type'] !== $segment->type) {
            return response()->json([
                'message' => 'Cannot change type of default segments'
            ], 422);
        }

        $segment->update($validated);

        return response()->json([
            'message' => 'Segment updated successfully',
            'segment' => $segment
        ]);
    }

    public function destroy(Business $business, UserSegment $segment)
    {
        if ($segment->is_default) {
            return response()->json([
                'message' => 'Cannot delete default segments'
            ], 422);
        }

        $segment->delete();
        return response()->json(['message' => 'Segment deleted successfully']);
    }

    public function previewCount(Business $business, UserSegment $segment)
    {
        $count = $segment->users()->count();
        return response()->json(['count' => $count]);
    }
}
