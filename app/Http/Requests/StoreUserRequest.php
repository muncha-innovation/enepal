<?php

namespace App\Http\Requests;

use App\Services\DocumentService;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        if (isset($this->user)) {
            $email_validation = 'unique:users,email,' . $this->user->id;
            $password_validation = 'nullable';
        } else if ($this->route()->getName() === 'profile.update'){
            $email_validation = 'unique:users,email,' . auth()->user()->id;
            $password_validation = 'nullable';
        } else {
            $email_validation = 'unique:users,email';
            $password_validation = 'nullable';
        }
        return array_merge([
            'email' => ['nullable', 'string', 'email', 'max:191', $email_validation],
            'password' => [$password_validation, 'confirmed', Rules\Password::defaults()],
            'last_name' => ['required', 'string', 'max:191'],
            'first_name' => ['required', 'string', 'max:191'],
            'phone' => ['nullable', 'string', 'max:191'],
            'address.country_id' => ['required'],
            'address.state_id' => ['nullable', 'exists:states,id'],
            'address.city' => ['nullable', 'string', 'max:50'],
            'address.address_line_1' => ['nullable', 'string', 'max:255'],
            'address.address_line_2' => ['nullable', 'string', 'max:255'],
            'address.postal_code' => ['nullable', 'string', 'max:50'],
            'address.prefecture' => ['nullable', 'string', 'max:50'],
            'address.town' => ['nullable', 'string', 'max:50'],
            'address.state' => ['nullable', 'string', 'max:50'],
            'address.street' => ['nullable', 'string', 'max:50'],
            'address.building' => ['nullable', 'string', 'max:50'],
            'image' => [
                'nullable',
                'image',
                'mimes:png,jpg,jpeg,svg,bmp',
                'max:2048',
            ],
            'role' => ['nullable', 'exists:roles,id'],
            'is_active' => ['nullable'],
            'dob' => ['nullable', 'date'],
            'has_passport' => ['nullable', 'boolean'],
            'education.*.type' => ['required', 'string', 'in:under_slc,slc,plus_two,bachelors,masters,phd,training'],
            'education.*.degree' => ['nullable', 'string', 'max:191'],
            'education.*.institution' => ['required', 'string', 'max:191'],
            'education.*.start_date' => ['required', 'date'],
            'education.*.end_date' => ['nullable'],
            'experience.*.job_title' => ['required', 'string', 'max:191'],
            'experience.*.company' => ['required', 'string', 'max:191'],
            'experience.*.start_date' => ['required', 'date'],
            'experience.*.end_date' => ['nullable', 'date'],
            'experience.*.description' => ['nullable', 'string'],
            'preferences.countries' => ['nullable', 'array'],
            'preferences.countries.*' => ['exists:countries,id'],
            'preferences.departure_date' => ['nullable', 'date'],
            'preferences.study_field' => ['nullable', 'string', 'max:255'],
            'preferences.app_language' => ['nullable', 'string', 'max:255'],
        ]);
    }

    protected function passedValidation()
    {
        if ($this->input('password')) {
            $this->merge([
                'password' => Hash::make($this->password),
            ]);
        } else if ($this->route()->getName() == 'profile.update') {
        
        } else {
            $password = \Str::random(8);
            $this->merge([
                'password' => Hash::make($password),
                'original_password' => $password,
            ]);
        }
        return $this;
    }
    public function validated(): array
    {
        
        $final = parent::validated();
        if ($this->input('password')) {
            $final = array_merge($final, ['password' => $this->input('password'), 'original_password' => $this->input('original_password')]);
        } else {
            unset($final['password']);
        }
        if ($this->route()->getName() === 'users.store') {
            $final['created_by'] = auth()->id();
        }
        return $final;
    }

    public function messages(): array
    {
        return [
            'role.exists' => 'The selected role is invalid.',
            'address.country_id.required' => 'The country field is required.',
            'address.state_id.exists' => 'The selected state is invalid.',
            'address.city.required' => 'The city field is required.',
            'address.city.string' => 'The city must be a string.',
            'address.city.max' => 'The city may not be greater than 50 characters.',
            'address.address_line_1.string' => 'The address line 1 must be a string.',
            'address.address_line_1.max' => 'The address line 1 may not be greater than 255 characters.',
            'address.address_line_2.string' => 'The address line 2 must be a string.',
            'address.address_line_2.max' => 'The address line 2 may not be greater than 255 characters.',
            'address.postal_code.string' => 'The postal code must be a string.',
            'address.postal_code.max' => 'The postal code may not be greater than 50 characters.',
            'address.prefecture.string' => 'The prefecture must be a string.',
            'address.prefecture.max' => 'The prefecture may not be greater than 50 characters.',
            'address.town.string' => 'The town must be a string.',
            'address.town.max' => 'The town may not be greater than 50 characters.',
            'address.postal_code.string' => 'The postal code must be a string.',
            'address.postal_code.max' => 'The postal code may not be greater than 50 characters.',
            'address.state.string' => 'The state must be a string.',
            'address.state.max' => 'The state may not be greater than 50 characters.',
            'address.street.string' => 'The street must be a string.',
            'address.street.max' => 'The street may not be greater than 50 characters.',
            'address.building.string' => 'The building must be a string.',
            'address.building.max' => 'The building may not be greater than 50 characters.',
            'image.image' => 'The image must be an image.',
            'image.mimes' => 'The image must be a file of type: png, jpg, jpeg, svg, bmp.',
            'image.max' => 'The image may not be greater than 2048 kilobytes.',
            'is_active.required' => 'The active field is required.',
            'role.required' => 'The role field is required.',
            'role.exists' => 'The selected role is invalid.',
            'email.required' => 'The email field is required.',
            'email.string' => 'The email must be a string.',
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'The email may not be greater than 191 characters.',
            'email.unique' => 'The email has already been taken.',
            'password.required' => 'The password field is required.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least 8 characters.',
            'last_name.required' => 'The last name field is required.',
            'last_name.string' => 'The last name must be a string.',
            'last_name.max' => 'The last name may not be greater than 191 characters.',
            'first_name.required' => 'The first name field is required.',
            'first_name.string' => 'The first name must be a string.',
            'first_name.max' => 'The first name may not be greater than 191 characters.',
            'phone.required' => 'The phone field is required.',
            'phone.string' => 'The phone must be a string.',
            'phone.max' => 'The phone may not be greater than 191 characters.',
        ];
    }
}
