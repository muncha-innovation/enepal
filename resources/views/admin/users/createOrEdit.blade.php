@extends('layouts.app')
@php
    if (isset($user)) {
        $isEdit = true;
        $title = 'Edit User';
        $action = route('admin.users.update', [$user]);
    } else {
        $isEdit = false;
        $title = 'Add User';
        $user = new App\Models\User();
        $action = route('admin.users.store');
    }
@endphp
@section('content')
    <section>
        <div class="bg-white p-4 shadow rounded">
            <form class="space-y-6" action="{{ $action }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif
                @include('modules.shared.success_error')
                {{-- choose role --}}
                <div class="mb-2">
                    <label for="role" class="block text-sm font-medium leading-6 text-gray-900">Role</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <select name="role" id="role"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            @foreach ($roles as $role)
                                <option value="{{ $role?->id }}" @if ($role?->id == $user->roles->first()?->id) selected @endif>
                                    {{ $role?->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-2">
                        <label for="dob"
                            class="block text-sm font-medium leading-6 text-gray-900">{{ __('Date of Birth') }}</label>
                        <div class="mt-2">
                @include('modules.shared.success_error')
                {{-- choose role --}}
                <div class="mb-2">
                    <label for="role" class="block text-sm font-medium leading-6 text-gray-900 required">Role</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <select name="role" id="role" required
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            @foreach ($roles as $role)
                                <option value="{{ $role?->id }}" @if ($role?->id == $user->roles->first()?->id) selected @endif>
                                    {{ $role?->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="validation-error" id="role-error">{{ __('Role is required') }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-2">
                        <label for="dob"
                            class="block text-sm font-medium leading-6 text-gray-900">{{ __('Date of Birth') }}</label>
                        <div class="mt-2">
                            <input type="date" name="dob" id="dob"
                                value="{{ old('dob', optional($user->dob)->format('Y-m-d')) }}"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="has_passport"
                            class="block text-sm font-medium leading-6 text-gray-900">{{ __('Has Passport') }}</label>
                        <div class="mt-2">
                            <select name="has_passport" id="has_passport"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300">
                                <option value="1" @if ($isEdit && $user->has_passport) selected @endif>Yes</option>
                                <option value="0" @if ($isEdit && !$user->has_passport) selected @endif>No</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-2 mt-2">
                    <label for="first_name"
                        class="block text-sm font-medium leading-6 text-gray-900 required">{{ __('First Name') }}</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" name="first_name" id="first_name" placeholder="Eg. John" required
                            value="{{ $user->first_name }}"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <div class="validation-error" id="first_name-error">{{ __('First name is required') }}</div>
                    </div>
                </div>
                <div class="mb-2">
                    <label for="last_name"
                        class="block text-sm font-medium leading-6 text-gray-900 required">{{ __('Last Name') }}</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" name="last_name" id="last_name" placeholder="Eg. Doe" required
                            value="{{ $user->last_name }}"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <div class="validation-error" id="last_name-error">{{ __('Last name is required') }}</div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="address[country_id]" class="block text-sm font-medium text-gray-700 required">
                            {{ __('Country') }}</label>
                        <div class="mt-1">
                            <select id="country" name="address[country_id]" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" @if ($country->id == $user->primaryAddress?->country->id) selected @endif>
                                        {{ $country->name }} ({{ $country->dial_code }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="validation-error" id="country-error">{{ __('Country is required') }}</div>
                        </div>
                    </div>
                    <div>
                        <label for="address[state_id]" class="block text-sm font-medium text-gray-700">
                            {{ __('State') }}</label>
                        <div class="mt-1">
                            <select id="state" name="address[state_id]"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @if (isset($user->primaryAddress?->state_id))
                                    <option value="{{ $user->primaryAddress?->state_id }}" selected>
                                        {{ $user->primaryAddress?->state->name }}
                                    </option>
                                @endif
                            </select>
                        </div>
                    </div>

                </div>

                <div class="mb-2">
                    <label for="address[city]" class="block text-sm font-medium leading-6 text-gray-900">City</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" name="address[city]" id="city"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            placeholder="Eg. Kathmandu" value="{{ $user->primaryAddress?->city }}">
                    </div>
                </div>

                <div class="mb-2">
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900 required">Email</label>
                    <input type="email" name="email" id="email" required
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        placeholder="Eg. abc@gmail.com" value="{{ $user->email }}">
                    <div class="validation-error" id="email-error">{{ __('Valid email is required') }}</div>
                </div>
                {{-- image --}}
                <div class="mb-2">
                    <label for="image" class="block text-sm font-medium leading-6 text-gray-900">Image</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="file" name="image" id="image"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                    @if ($isEdit && isset($user->profile_picture))
                        <div class="mt-2">
                            <img src="{{ getImage($user->profile_picture, 'profile/') }}" alt="user image"
                                class="w-20 h-20 rounded-md">
                    @endif
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 required">
                        {{ __('Phone Number') }}</label>
                    <div class="mt-1">
                        <input id="phone" name="phone" type="text" value="{{ $user->phone }}" required
                            minLength="6" maxLength="15"
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <div class="validation-error" id="phone-error">{{ __('Phone number is required (min 6, max 15 digits)') }}</div>
                    </div>
                </div>

                <div class="mb-2">
                    <label for="active" class="block text-sm font-medium leading-6 text-gray-900">Status</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <select name="is_active" id="active"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="1" @if ($user->is_active) selected @endif>Active</option>
                            <option value="0" @if (!$user->is_active) selected @endif>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4 mt-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('Education') }}</h3>
                        <button type="button" id="add-education"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('Add Education') }}
                        </button>
                    </div>

                    <div id="education-container" class="space-y-4">
                        @forelse($user->education ?? [] as $index => $education)
                            <div class="education-entry bg-gray-50 rounded-lg p-4 relative">
                                <button type="button"
                                    class="remove-entry absolute top-2 right-2 text-red-600 hover:text-red-800">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 required">{{ __('Type') }}</label>
                                        <select name="education[{{ $index }}][type]" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            @foreach (['under_slc' => 'Under SLC', 'slc' => 'SLC', 'plus_two' => '+2', 'bachelors' => 'Bachelors', 'masters' => 'Masters', 'phd' => 'PhD', 'training' => 'Training'] as $value => $label)
                                                <option value="{{ $value }}"
                                                    @if ($education->type === $value) selected @endif>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="validation-error" id="education-{{ $index }}-type-error">{{ __('Education type is required') }}</div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ __('Degree/Course') }}</label>
                                        <input type="text" name="education[{{ $index }}][degree]"
                                            value="{{ $education->degree }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 required">{{ __('Institution') }}</label>
                                        <input type="text" name="education[{{ $index }}][institution]" required
                                            value="{{ $education->institution }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <div class="validation-error" id="education-{{ $index }}-institution-error">{{ __('Institution is required') }}</div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 required">{{ __('Start Date') }}</label>
                                        <input type="date" name="education[{{ $index }}][start_date]" required
                                            value="{{ $education->start_date ? $education->start_date->format('Y-m-d') : '' }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <div class="validation-error" id="education-{{ $index }}-start_date-error">{{ __('Start date is required') }}</div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ __('End Date') }}</label>
                                        <input type="date" name="education[{{ $index }}][end_date]"
                                            value="{{ $education->end_date ? $education->end_date->format('Y-m-d') : '' }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4" id="no-education-message">
                                {{ __('No education records added yet') }}</p>
                        @endforelse
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4 mt-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('Work Experience') }}</h3>
                        <button type="button" id="add-experience"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('Add Experience') }}
                        </button>
                    </div>

                    <div id="experience-container" class="space-y-4">
                        @forelse($user->workExperience ?? [] as $index => $experience)
                            <div class="experience-entry bg-gray-50 rounded-lg p-4 relative">
                                <button type="button"
                                    class="remove-entry absolute top-2 right-2 text-red-600 hover:text-red-800">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 required">{{ __('Job Title') }}</label>
                                        <input type="text" name="experience[{{ $index }}][job_title]" required
                                            value="{{ $experience->job_title }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <div class="validation-error" id="experience-{{ $index }}-job_title-error">{{ __('Job title is required') }}</div>
                                    </div>

                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 required">{{ __('Company') }}</label>
                                        <input type="text" name="experience[{{ $index }}][company]" required
                                            value="{{ $experience->company }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <div class="validation-error" id="experience-{{ $index }}-company-error">{{ __('Company is required') }}</div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 required">{{ __('Start Date') }}</label>
                                        <input type="date" name="experience[{{ $index }}][start_date]" required
                                            value="{{ old("experience.{$index}.start_date", optional($experience->start_date)->format('Y-m-d')) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <div class="validation-error" id="experience-{{ $index }}-start_date-error">{{ __('Start date is required') }}</div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ __('End Date') }}</label>
                                        <input type="date" name="experience[{{ $index }}][end_date]"
                                            value="{{ old("experience.{$index}.end_date", optional($experience->end_date)->format('Y-m-d')) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4" id="no-experience-message">
                                {{ __('No work experience added yet') }}</p>
                        @endforelse
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4 mt-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('Preferences') }}</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="preferences[countries][]"
                                class="block text-sm font-medium text-gray-700">{{ __('Preferred Countries') }}</label>
                            <select id="preferred_countries" name="preferences[countries][]"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                multiple>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}"
                                        @if (isset($user->preference) && in_array($country->id, json_decode($user->preference->countries) ?? [])) selected @endif>{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="preferences[departure_date]"
                                class="block text-sm font-medium text-gray-700">{{ __('Departure Date') }}</label>
                            <input type="date" name="preferences[departure_date]" id="preferences[departure_date]"
                                value="{{ old('preferences.departure_date', optional($user->preference?->departure_date)->format('Y-m-d')) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="preferences[study_field]"
                                class="block text-sm font-medium text-gray-700">{{ __('Study Field') }}</label>
                            <input type="text" name="preferences[study_field]" id="preferences[study_field]"
                                value="{{ $user->preference->study_field ?? '' }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="preferences[app_language]"
                                class="block text-sm font-medium text-gray-700">{{ __('App Language') }}</label>
                            <input type="text" name="preferences[app_language]" id="preferences[app_language]"
                                value="{{ $user->preference->app_language ?? '' }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end w-full">
                    <div>
                        <button type="submit"
                            class="inline-block w-full px-8 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('js')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @include('modules.shared.state_prefill', ['entity' => $user, 'countries' => $countries])
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#preferred_countries').select2({
                placeholder: 'Select preferred countries',
                allowClear: true
            });
        });
        // Template for new education entry
        const educationTemplate = (index) => `
    <div class="education-entry bg-gray-50 rounded-lg p-4 relative">
        <button type="button" class="remove-entry absolute top-2 right-2 text-red-600 hover:text-red-800">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 required">Type</label>
                <select name="education[${index}][type]" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="under_slc">Under SLC</option>
                    <option value="slc">SLC</option>
                    <option value="plus_two">+2</option>
                    <option value="bachelors">Bachelors</option>
                    <option value="masters">Masters</option>
                    <option value="phd">PhD</option>
                    <option value="training">Training</option>
                </select>
                <div class="validation-error" id="education-${index}-type-error">{{ __('Education type is required') }}</div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Degree/Course') }}</label>
                <input type="text" name="education[${index}][degree]" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 required">{{ __('Institution') }}</label>
                <input type="text" name="education[${index}][institution]" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <div class="validation-error" id="education-${index}-institution-error">{{ __('Institution is required') }}</div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 required">{{ __('Start Date') }}</label>
                <input type="date" name="education[${index}][start_date]" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <div class="validation-error" id="education-${index}-start_date-error">{{ __('Start date is required') }}</div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('End Date') }}</label>
                <input type="date" name="education[${index}][end_date]" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>
    </div>
`;

        // Template for new experience entry
        const experienceTemplate = (index) => `
    <div class="experience-entry bg-gray-50 rounded-lg p-4 relative">
        <button type="button" class="remove-entry absolute top-2 right-2 text-red-600 hover:text-red-800">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 required">{{ __('Job Title') }}</label>
                <input type="text" name="experience[${index}][job_title]" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <div class="validation-error" id="experience-${index}-job_title-error">{{ __('Job title is required') }}</div>
            </div>
            
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 required">{{ __('Company') }}</label>
                <input type="text" name="experience[${index}][company]" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <div class="validation-error" id="experience-${index}-company-error">{{ __('Company is required') }}</div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 required">{{ __('Start Date') }}</label>
                <input type="date" name="experience[${index}][start_date]" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <div class="validation-error" id="experience-${index}-start_date-error">{{ __('Start date is required') }}</div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('End Date') }}</label>
                <input type="date" name="experience[${index}][end_date]" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>
    </div>
`;

        document.addEventListener('DOMContentLoaded', function() {
            // Add new education entry
            document.getElementById('add-education').addEventListener('click', function() {
                const container = document.getElementById('education-container');
                document.getElementById('no-education-message')?.remove();
                const index = container.children.length;
                container.insertAdjacentHTML('beforeend', educationTemplate(index));
            });

            // Add new experience entry
            document.getElementById('add-experience').addEventListener('click', function() {
                const container = document.getElementById('experience-container');
                document.getElementById('no-experience-message')?.remove();
                const index = container.children.length;
                container.insertAdjacentHTML('beforeend', experienceTemplate(index));
            });

            // Remove entry handler - using event delegation
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-entry')) {
                    if (confirm('Are you sure you want to remove this entry?')) {
                        e.target.closest('.education-entry, .experience-entry').remove();
                    }
                }
            });
        });
    </script>
@endpush
