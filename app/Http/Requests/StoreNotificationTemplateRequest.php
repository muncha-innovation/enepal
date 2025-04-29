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
        
        // Make validation rules for translatable fields more flexible
        foreach (config('app.supported_locales') as $locale) {
            // Make the fields nullable so empty fields won't cause validation failures
            $rules["subject.$locale"] = 'nullable|string';
            $rules["email_body.$locale"] = 'nullable|string';
        }

        return array_merge($rules, [
            'email_sent_from_name' => 'nullable|string',
            'email_sent_from_email' => 'nullable|string',
            'allow_business_section' => 'nullable|in:0,1',
        ]);
    }
    
    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check that at least one locale has required fields filled
            $hasSubject = false;
            $hasEmailBody = false;
            
            foreach (config('app.supported_locales') as $locale) {
                if (!empty($this->input("subject.$locale"))) {
                    $hasSubject = true;
                }
                
                if (!empty($this->input("email_body.$locale"))) {
                    $hasEmailBody = true;
                }
            }
            
            // Only enforce validation if no locales have any content
            if (!$hasSubject) {
                $validator->errors()->add('subject', __('At least one language must have a subject.'));
            }
            
            if (!$hasEmailBody) {
                $validator->errors()->add('email_body', __('At least one language must have an email body.'));
            }
        });
    }
}
