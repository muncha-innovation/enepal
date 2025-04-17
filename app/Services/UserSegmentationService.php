<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class UserSegmentationService
{
    public function getUsersBySegment($conditions)
    {
        $query = User::query();

        foreach ($conditions as $condition) {
            switch ($condition['type']) {
                case 'gender':
                    $query->where('gender', $condition['value']);
                    break;

                case 'age_range':
                    $minAge = $condition['value']['min'];
                    $maxAge = $condition['value']['max'];
                    $query->whereRaw('TIMESTAMPDIFF(YEAR, dob, CURDATE()) BETWEEN ? AND ?', [$minAge, $maxAge]);
                    break;

                case 'last_active':
                    $days = $condition['value'];
                    if ($condition['operator'] === 'more_than') {
                        $query->where('last_active_at', '<', Carbon::now()->subDays($days));
                    } else {
                        $query->where('last_active_at', '>', Carbon::now()->subDays($days));
                    }
                    break;

                case 'notification_opened':
                    $days = $condition['value'];
                    $query->whereHas('messages', function ($q) use ($days) {
                        $q->where('is_notification', true)
                          ->where('is_read', true)
                          ->where('updated_at', '>', Carbon::now()->subDays($days));
                    });
                    break;

                case 'user_type':
                    $query->whereHas('preference', function($q) use ($condition) {
                        $q->where('user_type', $condition['value']);
                    });
                    break;

                case 'has_passport':
                    $query->whereHas('preference', function($q) use ($condition) {
                        $q->where('has_passport', $condition['value']);
                    });
                    break;

                case 'interests':
                    $query->whereHas('newsPreferences', function($q) use ($condition) {
                        $q->whereIn('category_id', $condition['value']);
                    });
                    break;
            }
        }

        return $query;
    }

    public function getSegmentPreviewCount($conditions)
    {
        return $this->getUsersBySegment($conditions)->count();
    }
}
