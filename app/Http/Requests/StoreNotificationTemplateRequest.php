<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationTemplateRequest extends FormRequest
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
        $rules = [];
        foreach (config('app.supported_locales') as $locale) {
            $rules["subject.$locale"] = 'required|string';
            $rules["email_body.$locale"] = 'required|string';
        }

        return array_merge($rules, [
            'email_sent_from_name' => 'required|string',
            'email_sent_from_email' => 'required|email',
            'allow_business_section' => 'required|in:0,1',
        ]);
    }
}
