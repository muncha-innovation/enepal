<?php

namespace App\Http\Requests;

use App\Enums\SettingKeys;
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
            $coverImageValidation = 'sometimes|image|max:1999';
            $logoValidation = 'sometimes|image|max:1999';
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
                ];
                break;
                
            case 'details':
                $rules = [
                    'cover_image' => $this->isMethod('post') ? 'required|image|max:1999' : 'sometimes|image|max:1999',
                    'logo' => $this->isMethod('post') ? 'required|image|max:1999' : 'sometimes|image|max:1999',
                    'facilities' => ['sometimes', 'array'],
                    'facilities.*' => ['nullable', 'string', 'valid_facility_value'],
                    'hours' => 'sometimes|array',
                    'hours.*' => 'array',
                    'hours.*.is_open' => 'sometimes|boolean',
                    'hours.*.open_time' => 'exclude_if:hours.*.is_open,0|required_if:hours.*.is_open,1|nullable|date_format:H:i',
                    'hours.*.close_time' => 'exclude_if:hours.*.is_open,0|required_if:hours.*.is_open,1|nullable|date_format:H:i',
                ];
                break;
                
            case 'address':
                $rules = [
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
                ];
                break;
                
            case 'contact':
                $rules = [
                    'email' => ['required', 'email', 'unique:businesses,email,' . $this->route('business')],
                    'phone_1' => ['required'],
                    'phone_2' => ['sometimes'],
                    'is_active' => ['required'],
                    'established_year' => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
                    'custom_email_message' => ['nullable', 'string', 'max:1000'],
                    'languages' => ['nullable', 'array'],
                    'languages.*.id' => ['required_with:languages', 'exists:languages,id'],
                    'languages.*.price' => ['required_with:languages', 'numeric', 'min:0'],
                    'languages.*.num_people_taught' => ['nullable', 'numeric', 'min:0'],
                    'languages.*.level' => ['nullable', 'in:beginner,intermediate,advanced'],
                    'languages.*.currency' => ['required_with:languages', 'string', 'size:3'],
                    'destinations' => ['nullable', 'array'],
                    'destinations.*.country_id' => ['required_with:destinations', 'exists:countries,id'],
                    'destinations.*.num_people_sent' => ['nullable', 'numeric', 'min:0'],
                    'settings' => ['sometimes'],
                    'social_networks' => 'nullable|array',
                    'social_networks.*.network_id' => 'required_with:social_networks.*.url|exists:social_networks,id',
                    'social_networks.*.url' => 'nullable|string',
                    'social_networks.*.is_active' => 'boolean',
                ];
                break;
                
            default:
                // If no section specified, use all rules for a complete form submission
                $rules = [
                    'name' => ['required'],
                    'email' => ['required', 'email'],
                    'type_id' => ['required'],
                    'phone_1' => ['required'],
                    'is_active' => ['required'],
                    'description' => ['array', 'sometimes'],
                    'cover_image' => $coverImageValidation,
                    'logo' => $logoValidation,
                    'established_year' => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
                    'phone_2' => ['sometimes'],
                    'address.city' => ['sometimes'],
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
                    'settings' => ['sometimes'],
                    'facilities' => ['sometimes', 'array'],
                    'facilities.*' => ['nullable', 'string', 'valid_facility_value'],
                    'custom_email_message' => ['nullable', 'string', 'max:1000'],
                    'languages' => ['nullable', 'array'],
                    'languages.*.id' => ['required_with:languages', 'exists:languages,id'],
                    'languages.*.price' => ['required_with:languages', 'numeric', 'min:0'],
                    'languages.*.num_people_taught' => ['nullable', 'numeric', 'min:0'],
                    'languages.*.level' => ['nullable', 'in:beginner,intermediate,advanced'],
                    'languages.*.currency' => ['required_with:languages', 'string', 'size:3'],
                    'destinations' => ['nullable', 'array'],
                    'destinations.*.country_id' => ['required_with:destinations', 'exists:countries,id'],
                    'destinations.*.num_people_sent' => ['nullable', 'numeric', 'min:0'],
                    'location' => ['sometimes', 'string'],
                    'hours' => 'sometimes|array',
                    'hours.*' => 'array',
                    'hours.*.is_open' => 'sometimes|boolean',
                    'hours.*.open_time' => 'exclude_if:hours.*.is_open,0|required_if:hours.*.is_open,1|nullable|date_format:H:i',
                    'hours.*.close_time' => 'exclude_if:hours.*.is_open,0|required_if:hours.*.is_open,1|nullable|date_format:H:i',
                    'social_networks' => 'nullable|array',
                    'social_networks.*.network_id' => 'required_with:social_networks.*.url|exists:social_networks,id',
                    'social_networks.*.url' => 'nullable|string',
                    'social_networks.*.is_active' => 'boolean',
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
                        'is_active' => $item['is_active'] ?? true
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
        // Remove whitespace and convert to lowercase
        $url = trim(strtolower($url));
        
        // Get network info from database
        $network = \App\Models\SocialNetwork::find($networkId);
        if (!$network) return $url;

        switch ($network->name) {
            case 'Facebook':
                // Handle Facebook URLs
                if (preg_match('/^@/', $url)) {
                    // Handle @username format
                    $username = ltrim($url, '@');
                    return "https://www.facebook.com/{$username}";
                }
                if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
                    // Add https:// if not present
                    $url = "https://" . ltrim($url, '/');
                }
                // Ensure www.facebook.com format
                $url = preg_replace('~^(?:https?://)?(?:www\.)?facebook\.com~i', 'https://www.facebook.com', $url);
                break;

            case 'Twitter':
                // Handle Twitter URLs
                if (preg_match('/^@/', $url)) {
                    $username = ltrim($url, '@');
                    return "https://twitter.com/{$username}";
                }
                if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
                    $url = "https://" . ltrim($url, '/');
                }
                $url = preg_replace('~^(?:https?://)?(?:www\.)?twitter\.com~i', 'https://twitter.com', $url);
                break;

            case 'Instagram':
                // Handle Instagram URLs
                if (preg_match('/^@/', $url)) {
                    $username = ltrim($url, '@');
                    return "https://www.instagram.com/{$username}";
                }
                if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
                    $url = "https://" . ltrim($url, '/');
                }
                $url = preg_replace('~^(?:https?://)?(?:www\.)?instagram\.com~i', 'https://www.instagram.com', $url);
                break;

            case 'LinkedIn':
                // Handle LinkedIn URLs
                if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
                    $url = "https://" . ltrim($url, '/');
                }
                $url = preg_replace('~^(?:https?://)?(?:www\.)?linkedin\.com~i', 'https://www.linkedin.com', $url);
                break;

            // Add more cases for other networks as needed
        }

        return $url;
    }
}
