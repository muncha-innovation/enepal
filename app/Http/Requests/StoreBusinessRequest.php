<?php

namespace App\Http\Requests;

use App\Enums\SettingKeys;
use App\Models\SocialNetwork;
use App\Rules\ValidBusinessHours;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Determine which section is being saved
        $section = $this->input('_section', 'all');
        
        // cover_image and logo required only during create and not edit
        if ($this->isMethod('post')) {
            $coverImageValidation = 'required|image|max:1999';
            $logoValidation = 'required|image|max:1999';
        } else {
            $coverImageValidation = 'nullable|image|max:1999';
            $logoValidation = 'nullable|image|max:1999';
        }
        
        // Base rules that always apply
        $rules = [];
        
        // Section-specific rules
        switch ($section) {
            case 'general':
                $rules = [
                    'name' => ['required'],
                    'type_id' => ['required'],
                    'description' => ['array', 'sometimes'],
                    'logo' => $logoValidation, 
                    'cover_image' => $coverImageValidation, 
                    'established_year' => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
                    'custom_email_message' => ['nullable', 'string', 'max:1000'],
                    'is_active' => ['sometimes'],
                    'max_notifications_per_day' => ['nullable', 'integer', 'min:0'],
                    'max_notifications_per_month' => ['nullable', 'integer', 'min:0'],
                ];
                break;
                
            case 'details':
                $rules = [
                    'facilities' => ['sometimes', 'array'],
                    'facilities.*' => ['nullable', 'string', 'valid_facility_value'],
                    'hours' => ['sometimes', 'array', new ValidBusinessHours],
                ];
                break;
                
            case 'address':
                $rules = [
                    'address.city' => ['required'],
                    'address.state_id' => ['nullable'],
                    'address.street' => ['nullable'],
                    'address.postal_code' => ['nullable'],
                    'address.address_line_1' => ['nullable'],
                    'address.address_line_2' => ['nullable'],
                    'address.country_id' => ['required'],
                    'address.prefecture' => ['nullable'],
                    'address.town' => ['nullable'],
                    'address.building' => ['nullalbe'],
                    'address.location' => ['nullable', 'string'],
                    'email' => ['required', 'email'],
                    'phone_1' => ['required'],
                    'phone_2' => ['nullable'],
                    'contact_person_name' => ['nullable']
                ];
                break;
                
            case 'social_media':
                $rules = [
                    'social_networks' => 'nullable|array',
                    'social_networks.*.network_id' => 'required_with:social_networks.*.url|exists:social_networks,id',
                    'social_networks.*.url' => 'nullable|string',
                    'social_networks.*.is_active' => 'boolean',
                ];
                break;
                
            case 'manpower_consultancy':
                // dd($this->all());
                $rules = [
                    'languages' => ['nullable', 'array'],
                    'languages.*.id' => ['required_with:languages', 'exists:languages,id'],
                    'languages.*.currency' => ['nullable'],
                    'languages.*.price' => ['nullable', 'numeric'],
                    'destinations' => ['nullable', 'array'],
                    'destinations.*.country_id' => ['required_with:destinations', 'exists:countries,id'],
                    'destinations.*.num_people_sent' => ['nullable','integer'],
                ];
                break;
                
            default:
                // If no section specified, use all rules for a complete form submission
                $rules = [
                    // General section
                    'name' => ['required'],
                    'type_id' => ['required'],
                    'description' => ['array', 'sometimes'],
                    'logo' => $logoValidation,
                    'cover_image' => $coverImageValidation,
                    'established_year' => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
                    'custom_email_message' => ['nullable', 'string', 'max:1000'],
                    'is_active' => ['sometimes'],
                    'max_notifications_per_day' => ['nullable', 'integer', 'min:0'],
                    'max_notifications_per_month' => ['nullable', 'integer', 'min:0'],
                    
                    // Details section
                    'facilities' => ['sometimes', 'array'],
                    'facilities.*' => ['nullable', 'string', 'valid_facility_value'],
                    'hours' => ['sometimes', 'array', new ValidBusinessHours],
                    
                    // Address section
                    'address.city' => ['required'],
                    'address.state_id' => ['sometimes'],
                    'address.street' => ['sometimes'],
                    'address.postal_code' => ['sometimes'],
                    'address.address_line_1' => ['sometimes'],
                    'address.address_line_2' => ['sometimes'],
                    'address.country_id' => ['required'],
                    'address.prefecture' => ['sometimes'],
                    'address.town' => ['sometimes'],
                    'address.building' => ['sometimes'],
                    'address.location' => ['sometimes', 'string'],
                    'email' => ['required', 'email'],
                    'phone_1' => ['required'],
                    'phone_2' => ['sometimes'],
                    'contact_person_name' => ['sometimes'],
                    
                    // Social Media section
                    'social_networks' => 'nullable|array',
                    'social_networks.*.network_id' => 'required_with:social_networks.*.url|exists:social_networks,id',
                    'social_networks.*.url' => 'nullable|string',
                    'social_networks.*.is_active' => 'boolean',
                    
                    // Manpower Consultancy section
                    'languages' => ['nullable', 'array'],
                    'languages.*.language_id' => ['required_with:languages', 'exists:languages,id'],
                    'languages.*.level' => ['nullable', 'in:beginner,intermediate,advanced,fluent'],
                    'destinations' => ['nullable', 'array'],
                    'destinations.*.country_id' => ['required_with:destinations', 'exists:countries,id'],
                    'destinations.*.description' => ['nullable', 'string'],
                    
                    // Settings
                    'settings' => ['sometimes'],
                ];
                break;
        }
        return $rules;
    }

    protected function prepareForValidation()
    {
        $hours = $this->input('hours', []);
        $cleanedHours = [];
        
        foreach ($hours as $day => $schedule) {
            if (isset($schedule['is_open']) && $schedule['is_open']) {
                $cleanedHours[$day] = $schedule;
            }
        }
        
        if (!empty($cleanedHours)) {
            $this->merge(['hours' => $cleanedHours]);
        }

    }

    public function messages()
    {
        return [
            'name.required' => 'Business name is required',
            'email.required' => 'Business email is required',
            'type_id.required' => 'Business type is required',
            'phone_1.required' => 'Business phone number is required',
            'is_active.required' => 'Business status is required',
            'cover_image.required' => 'Business cover image is required',
            'logo.required' => 'Business logo is required',
            'address.country_id.required' => 'Country is required',
            'custom_email_message.max' => 'Custom email message cannot be longer than 1000 characters',
            'hours.valid_business_hours' => 'Please enter both opening and closing times for selected days.',
        ];
    }

    public function validated()
    {
        $data = parent::validated();
        $default_settings = [
            SettingKeys::MAX_NOTIFICATION_PER_DAY => 0,
            SettingKeys::MAX_NOTIFICATION_PER_MONTH => 0,
        ];    
        $data['settings'] = isset($data['settings']) ? $data['settings'] : $default_settings;
        
        if ($this->isMethod('post')) {
            $data['created_by'] = auth()->id();

        }

        // Convert location string to Point if provided
        if (isset($data['location']) && is_string($data['location'])) {
            preg_match('/POINT\((.*?)\)/', $data['location'], $matches);
            if (isset($matches[1])) {
                list($lng, $lat) = explode(' ', $matches[1]);
                $data['location'] = new Point($lat, $lng);
            }
        }

        // Format social networks data if present
        if (isset($data['social_networks'])) {
            $data['social_networks'] = collect($data['social_networks'])
                ->filter(function ($item) {
                    // Only include items that have both network_id and url
                    return !empty($item['url']) && isset($item['network_id']);
                })
                ->mapWithKeys(function ($item) {
                    return [$item['network_id'] => [
                        'url' => $this->normalizeUrl($item['url'], $item['network_id']),
                        'is_active' => isset($item['is_active']) ? (bool)$item['is_active'] : false
                    ]];
                })->all();
        }

        return $data;
    }

    /**
     * Normalize social media URL based on network
     */
    protected function normalizeUrl($url, $networkId)
    {
        $url = trim(strtolower($url));
        
        $network = SocialNetwork::find($networkId);
        
        if (!$network) return $url;

        return $this->normalizeSocialUrl($url, strtolower($network->name));

    }

    function normalizeSocialUrl($url, $platform = 'facebook')
    {
        $url = trim($url);

        // Empty input → nothing to normalise
        if ($url === '') {
            return null;
        }

        // If the user already provided a full URL (e.g. https://facebook.com/ujwal),
        // return it untouched to avoid double-prefixing.
        if (preg_match('~^(?:f|ht)tps?://~i', $url)) {
            return $url;
        }

        // Extract a username/identifier from shorthand input
        if (strpos($url, '@') === 0) {
            $username = ltrim($url, '@');
        } else {
            // Treat anything without protocol as a raw username/path and trim leading slashes
            $username = ltrim($url, '/');
        }

        $platformUrls = [
            'facebook'  => 'https://www.facebook.com/%s',
            'instagram' => 'https://www.instagram.com/%s',
            'tiktok'    => 'https://www.tiktok.com/@%s',
            'twitter'   => 'https://twitter.com/%s',
            'x'         => 'https://x.com/%s',
            'linkedin'  => 'https://www.linkedin.com/in/%s',
            'youtube'   => 'https://www.youtube.com/%s',
            'whatsapp'  => 'https://wa.me/%s',
            'snapchat'  => 'https://www.snapchat.com/add/%s',
            'viber'     => 'viber://add?number=%s',
        ];

        // Build the full URL using the mapping table if one exists for this platform
        if (isset($platformUrls[$platform])) {
            return sprintf($platformUrls[$platform], $username);
        }

        // Fallback – return the original input
        return $url;
    }

}
