<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\UserSegment;
use App\Services\UserSegmentationService;
use Illuminate\Http\Request;

class SegmentController extends Controller
{
    protected $segmentationService;

    public function __construct(UserSegmentationService $segmentationService)
    {
        $this->segmentationService = $segmentationService;
    }

    public function previewCount($segmentId)
    {
        // Handle predefined segments
        if (!str_contains($segmentId, 'custom_')) {
            $predefinedSegments = [
                'recently_active' => [['type' => 'last_active', 'operator' => 'less_than', 'value' => 7]],
                'inactive' => [['type' => 'last_active', 'operator' => 'more_than', 'value' => 30]],
                'engaged' => [['type' => 'notification_opened', 'value' => 7]],
                'students' => [['type' => 'user_type', 'value' => 'student']],
                'job_seekers' => [['type' => 'user_type', 'value' => 'job_seeker']]
            ];

            $conditions = $predefinedSegments[$segmentId] ?? [];
            $count = $this->segmentationService->getSegmentPreviewCount($conditions);
        } else {
            // Handle custom segments
            $segmentId = str_replace('custom_', '', $segmentId);
            $segment = UserSegment::findOrFail($segmentId);
            $count = $this->segmentationService->getSegmentPreviewCount($segment->conditions);
        }

        return response()->json(['count' => $count]);
    }

    public function store(Request $request, Business $business)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'conditions' => 'required|array',
            'is_active' => 'boolean'
        ]);

        $segment = $business->userSegments()->create($validated);

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
            'conditions' => 'required|array',
            'is_active' => 'boolean'
        ]);

        $segment->update($validated);

        return response()->json([
            'message' => 'Segment updated successfully',
            'segment' => $segment
        ]);
    }

    public function destroy(Business $business, UserSegment $segment)
    {
        $segment->delete();
        return response()->json(['message' => 'Segment deleted successfully']);
    }
}
