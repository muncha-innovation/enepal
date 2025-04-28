<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSegment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class UserSegmentationService
{
    /**
     * Get users filtered by a set of conditions (segment)
     *
     * @param array $conditions
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getUsersBySegment(array $conditions)
    {
        $query = User::query();
        
        foreach ($conditions as $condition) {
            $this->applyCondition($query, $condition);
        }
        
        return $query;
    }
    
    /**
     * Get users for a custom segment
     *
     * @param \App\Models\UserSegment $segment
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getUsersForSegment(UserSegment $segment)
    {
        return $this->getUsersBySegment($segment->conditions);
    }
    
    /**
     * Get users for a predefined segment
     *
     * @param string $segmentId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getUsersForPredefinedSegment(string $segmentId)
    {
        $predefinedSegments = [
            'recently_active' => [['type' => 'last_active', 'operator' => 'less_than', 'value' => 7]],
            'inactive' => [['type' => 'last_active', 'operator' => 'more_than', 'value' => 30]],
            'engaged' => [['type' => 'notification_opened', 'value' => 7]],
            'students' => [['type' => 'user_type', 'value' => 'student']],
            'job_seekers' => [['type' => 'user_type', 'value' => 'job_seeker']]
        ];
        
        $conditions = $predefinedSegments[$segmentId] ?? [];
        return $this->getUsersBySegment($conditions);
    }
    
    /**
     * Apply a single condition to the query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $condition
     */
    protected function applyCondition(Builder $query, array $condition)
    {
        $type = $condition['type'] ?? null;
        
        switch ($type) {
            case 'last_active':
                $this->applyLastActiveCondition($query, $condition);
                break;
                
            case 'user_type':
                $this->applyUserTypeCondition($query, $condition);
                break;
                
            case 'notification_opened':
                $this->applyNotificationOpenedCondition($query, $condition);
                break;
                
            case 'location':
                $this->applyLocationCondition($query, $condition);
                break;
                
            case 'age':
                $this->applyAgeCondition($query, $condition);
                break;

            case 'gender':
                $this->applyGenderCondition($query, $condition);
                break;
        }
    }
    
    /**
     * Apply last active date condition
     */
    protected function applyLastActiveCondition(Builder $query, array $condition)
    {
        $operator = $condition['operator'] ?? 'less_than';
        $days = $condition['value'] ?? 7;
        
        $compareDate = Carbon::now()->subDays($days);
        
        if ($operator === 'less_than') {
            $query->where('last_active_at', '>=', $compareDate);
        } else {
            $query->where(function($q) use ($compareDate) {
                $q->where('last_active_at', '<', $compareDate)
                  ->orWhereNull('last_active_at');
            });
        }
    }
    
    /**
     * Apply user type condition
     */
    protected function applyUserTypeCondition(Builder $query, array $condition)
    {
        $userType = $condition['value'] ?? null;
        
        if ($userType) {
            $query->whereHas('preference', function($q) use ($userType) {
                $q->where('user_type', $userType);
            });
        }
    }
    
    /**
     * Apply notification opened condition
     */
    protected function applyNotificationOpenedCondition(Builder $query, array $condition)
    {
        $days = $condition['value'] ?? 7;
        $minCount = $condition['min_count'] ?? 1;
        
        $query->whereHas('businessNotifications', function($q) use ($days, $minCount) {
            $q->whereNotNull('business_notifications_users.read_at')
              ->where('business_notifications_users.read_at', '>=', Carbon::now()->subDays($days))
              ->havingRaw('COUNT(*) >= ?', [$minCount])
              ->groupBy('business_notifications_users.user_id');
        });
    }
    
    /**
     * Apply location condition
     */
    protected function applyLocationCondition(Builder $query, array $condition)
    {
        $countryId = $condition['country_id'] ?? null;
        $stateId = $condition['state_id'] ?? null;
        $city = $condition['city'] ?? null;
        
        $query->whereHas('addresses', function($q) use ($countryId, $stateId, $city) {
            if ($countryId) {
                $q->where('country_id', $countryId);
            }
            
            if ($stateId) {
                $q->where('state_id', $stateId);
            }
            
            if ($city) {
                $q->where('city', 'like', "%{$city}%");
            }
        });
    }
    
    /**
     * Apply age condition
     */
    protected function applyAgeCondition(Builder $query, array $condition)
    {
        $minAge = $condition['min'] ?? null;
        $maxAge = $condition['max'] ?? null;
        
        if ($minAge !== null || $maxAge !== null) {
            $query->whereNotNull('dob');
            
            if ($minAge !== null) {
                $maxDate = Carbon::now()->subYears($minAge)->format('Y-m-d');
                $query->where('dob', '<=', $maxDate);
            }
            
            if ($maxAge !== null) {
                $minDate = Carbon::now()->subYears($maxAge + 1)->addDay()->format('Y-m-d');
                $query->where('dob', '>=', $minDate);
            }
        }
    }
    
    /**
     * Apply gender condition
     */
    protected function applyGenderCondition(Builder $query, array $condition)
    {
        $gender = $condition['value'] ?? null;
        
        if ($gender) {
            $query->whereHas('preference', function($q) use ($gender) {
                $q->where('gender', $gender);
            });
        }
    }
    
    /**
     * Get preview count of users matching a segment
     *
     * @param array $conditions
     * @return int
     */
    public function getSegmentPreviewCount(array $conditions)
    {
        return $this->getUsersBySegment($conditions)->count();
    }
}
