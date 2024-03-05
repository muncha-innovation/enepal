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
        return auth()->user()->isSuperAdmin();
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
            $username_validation = 'unique:users,user_name,' . $this->user->id;
            $password_validation = 'nullable';
        } else {
            $email_validation = 'unique:users,email';
            $username_validation = 'unique:users,user_name';
            $password_validation = 'nullable';
        }
        return [
            'user_name' => ['required', 'string', 'max:191', $username_validation],
            'email' => ['nullable', 'string', 'email', 'max:191', $email_validation],
            'password' => [$password_validation, 'confirmed', Rules\Password::defaults()],
            'last_name' => ['required', 'string', 'max:191'],
            'first_name' => ['required', 'string', 'max:191'],
            'p_last_name' => ['nullable', 'string', 'max:191'],
            'p_first_name' => ['nullable', 'string', 'max:191'],
            'mobile' => ['nullable', 'string', 'max:191'],
            'address.country' => 'required',
            'postalCode1' => ['nullable', 'string', 'max: 20'],
            'postalCode2' => ['nullable', 'string', 'max: 20'],
            'address.city' => 'nullable | string| max: 50',
            'address.prefecture' => ['nullable', 'string', 'max:50'],
            'address.town' => ['nullable', 'string', 'max:50'],
            'address.postal_code' => ['nullable', 'string', 'max:50'],
            'address.state' => ['nullable', 'string', 'max:50'],
            'address.street' => ['nullable', 'string', 'max:50'],
            'address.building' => ['nullable', 'string', 'max:50'],
            'image' => [
                'nullable',
                'image',
                'mimes:png,jpg,jpeg,svg,bmp',
                'max:2048',
            ],
            'is_active' => 'required',
            'role' => ['required', 'exists:roles,id'],
            'departmentids' => 'nullable|sometimes|array',
            'departmentids.*' => 'exists:branches,id',
        ];
    }

    protected function passedValidation()
    {
        if ($this->input('password')) {
            $this->merge([
                'password' => Hash::make($this->password),
            ]);
        }
        if ($this->has('image')) {
            $documentService = new DocumentService();
            $url = $documentService->store($this->image, 'users');
            $this->merge([
                'image' => $url,
            ]);
        }
        return $this;
    }
    public function validated(): array
    {
        $final = parent::validated();
        if ($this->input('password')) {

            $final = array_merge($final, ['password' => $this->input('password')]);
        } else {
            unset($final['password']);
        }
        if ($this->has('image')) {
            $final = array_merge($final, ['image' => $this->input('image')]);
        }
        if ($this->has('postalCode1') && $this->has('postalCode2')) {
            $final['address']['postal_code'] = $this->input('postalCode1') . '-' . $this->input('postalCode2');
            unset($final['postalCode1']);
            unset($final['postalCode2']);
        }
        if ($this->route()->getName() === 'users.store') {
            $final['user_id'] = auth()->id();
        }
        return $final;
    }
}