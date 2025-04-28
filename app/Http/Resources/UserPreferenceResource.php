<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserPreferenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $knownLanguages = $this->known_languages;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_type' => $this->user_type,
            'app_language' => $this->app_language,
            'known_languages' => is_array($knownLanguages) ? $knownLanguages : [],
            'countries' => $this->countries,
           
            'departure_date' => $this->departure_date?->format('Y-m-d'),
            'study_field' => $this->study_field,
            'has_passport' => (bool) $this->has_passport,
            'passport_expiry' => $this->passport_expiry?->format('Y-m-d'),
            'receive_notifications' => (bool) $this->receive_notifications,
            'show_personalized_content' => (bool) $this->show_personalized_content,
            'distance_unit' => $this->distance_unit ?? 'km',
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}