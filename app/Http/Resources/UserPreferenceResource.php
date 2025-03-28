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
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_type' => $this->user_type,
            'countries' => $this->countries,
            'study_field' => $this->study_field,
            'departure_date' => $this->departure_date,
            'app_language' => $this->app_language,
            'known_languages' => $this->known_languages,
            'has_passport' => $this->has_passport,
            'passport_expiry' => $this->passport_expiry,
            'receive_notifications' => $this->receive_notifications,
            'show_personalized_content' => $this->show_personalized_content,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}