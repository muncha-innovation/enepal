<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BusinessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $lang = $request->query('lang') ?? 'en';
        
        // Get social networks from the relationship
        $socialLinks = [
            'facebook' => ['url' => null, 'icon' => null],
            'instagram' => ['url' => null, 'icon' => null],
            'website' => ['url' => $this->website ?? null, 'icon' => null],
        ];

        foreach ($this->socialNetworks as $network) {
            if ($network->pivot->is_active) {
                $socialLinks[strtolower($network->name)] = [
                    'url' => $network->pivot->url,
                    'icon' => $network->icon
                ];
            }
        }

        return [
            'id' => $this->id,
            'title' => $this->name,
            'description' => $this->getTranslation('description', $lang),
            'cover_image' => getImage($this->cover_image, 'business/cover_image/'),
            'profile_image' => getImage($this->logo, 'business/logo/'),
            'phone' => $this->phone1,
            'email' => $this->email,
            'website' => $this->website,
            'has_followed' => $this->has_followed,
            'is_admin' => $this->is_admin || $this->is_owner,
            
            // Only include facilities with value = true
            'facilities' => $this->facilities
                ->filter(function($facility) {
                    return filter_var($facility->pivot->value, FILTER_VALIDATE_BOOLEAN);
                })
                ->map(function($facility) {
                    return [
                        'id' => $facility->id,
                        'title' => $facility->name,
                        'icon' => getImage($facility->icon)
                    ];
                }),
            
            // Social media links from dedicated relationship
            'social_links' => $socialLinks,
            
            // Hours formatting
            'formatted_hours' => $this->formatted_hours,
            'hours' => $this->whenLoaded('hours', function() {
                return $this->hours->map(function($hour) {
                    return [
                        'day' => $hour->day,
                        'is_open' => $hour->is_open,
                        'open_time' => $hour->open_time ? \Carbon\Carbon::parse($hour->open_time)->format('H:i') : null,
                        'close_time' => $hour->close_time ? \Carbon\Carbon::parse($hour->close_time)->format('H:i') : null,
                    ];
                });
            }),
            
            // Relationships and other fields
            'address' => new AddressResource($this->whenLoaded('address')),
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'type' => BusinessTypesResource::make($this->whenLoaded('type')),
            'galleries' => GalleryResource::collection($this->whenLoaded('galleries')),
            'distance' => $this->when(isset($this->distance), function() {
                return round($this->distance, 2);
            }),
        ];
    }
}
