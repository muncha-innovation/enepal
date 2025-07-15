@extends('layouts.app')
@php
    if (isset($user)) {
        $isEdit = true;
        $title = __('Edit User');
        $action = route('admin.users.update', [$user]);
    } else {
        $isEdit = false;
        $title = __('Add User');
        $user = new App\Models\User();
        $action = route('admin.users.store');
    }
@endphp
@section('content')
    <section>
        <div class="bg-white p-4 shadow rounded">
            <h1 class="text-2xl font-semibold text-gray-700 mb-4">{{ $title }}</h1>

            <!-- Tab Navigation -->
            <div class="border-b border-gray-200 mb-6">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="userTabs" role="tablist">
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 border-indigo-600 rounded-t-lg text-indigo-600" 
                            id="general-tab" data-tab="general" type="button" role="tab" aria-selected="true">
                            {{ __('General') }}
                        </button>
                    </li>
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" 
                            id="education-tab" data-tab="education" type="button" role="tab" aria-selected="false">
                            {{ __('Education') }}
                        </button>
                    </li>
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" 
                            id="experience-tab" data-tab="experience" type="button" role="tab" aria-selected="false">
                            {{ __('Work Experience') }}
                        </button>
                    </li>
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" 
                            id="preferences-tab" data-tab="preferences" type="button" role="tab" aria-selected="false">
                            {{ __('Preferences') }}
                        </button>
                    </li>
                </ul>
            </div>

            <form class="space-y-6" action="{{ $action }}" method="POST" enctype="multipart/form-data" id="userForm" novalidate>
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif
                @include('modules.shared.success_error')

                <div class="tab-content">
                    <!-- General Tab -->
                    <div id="general" class="tab-pane active">
                        <div class="space-y-6">
                            <!-- Role Selection -->
                            <div class="mb-2">
                                <label for="role" class="block text-sm font-medium leading-6 text-gray-900 required">{{ __('Role') }}</label>
                                <div class="mt-2 rounded-md shadow-sm">
                                    <select name="role" id="role" required
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option value="">{{ __('Select Role') }}</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role?->id }}" @if ($role?->id == $user->roles->first()?->id) selected @endif>
                                                {{ $role?->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="validation-error" id="role-error">{{ __('Role is required') }}</div>
                                </div>
                            </div>

                            <!-- Basic Information -->
                            <h3 class="text-lg font-medium text-gray-700 mb-3">{{ __('Basic Information') }}</h3>
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
                                            <option value="1" @if ($isEdit && $user->has_passport) selected @endif>{{ __('Yes') }}</option>
                                            <option value="0" @if ($isEdit && !$user->has_passport) selected @endif>{{ __('No') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-2 mt-2">
                                <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900 required">{{ __('First Name') }}</label>
                                <div class="mt-2 rounded-md shadow-sm">
                                    <input type="text" name="first_name" id="first_name" placeholder="{{ __('Eg. John') }}" required
                                        value="{{ $user->first_name }}"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <div class="validation-error" id="first_name-error">{{ __('First name is required') }}</div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="last_name" class="block text-sm font-medium leading-6 text-gray-900 required">{{ __('Last Name') }}</label>
                                <div class="mt-2 rounded-md shadow-sm">
                                    <input type="text" name="last_name" id="last_name" placeholder="{{ __('Eg. Doe') }}" required
                                        value="{{ $user->last_name }}"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <div class="validation-error" id="last_name-error">{{ __('Last name is required') }}</div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <h3 class="text-lg font-medium text-gray-700 mb-3">{{ __('Contact Information') }}</h3>
                            <div class="space-y-4">
                                <div class="mb-2">
                                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900 required">{{ __('Email') }}</label>
                                    <input type="email" name="email" id="email" required
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        placeholder="{{ __('Eg. abc@gmail.com') }}" value="{{ $user->email }}">
                                    <div class="validation-error" id="email-error">{{ __('Valid email address is required') }}</div>
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 required">
                                        {{ __('Phone Number') }}</label>
                                    <div class="mt-1">
                                        <input id="phone" name="phone" type="text" value="{{ $user->phone }}" required
                                            minLength="6" maxLength="15"
                                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <div class="validation-error" id="phone-error">{{ __('Phone number is required') }}</div>
                                    </div>
                                </div>

                                <!-- Password Fields -->
                                <div class="mb-2">
                                    <label for="password" class="block text-sm font-medium leading-6 text-gray-900 @if(!$isEdit) required @endif">
                                        {{ $isEdit ? __('New Password') : __('Password') }}
                                    </label>
                                    <div class="mt-2">
                                        <input type="password" name="password" id="password" 
                                            @if(!$isEdit) required @endif
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                            placeholder="{{ $isEdit ? __('Leave blank to keep current password') : __('Enter password') }}">
                                        <div class="validation-error" id="password-error">
                                            {{ !$isEdit ? __('Password is required') : __('Password must be at least 8 characters') }}
                                        </div>
                                        @if($isEdit)
                                            <p class="mt-1 text-sm text-gray-500">{{ __('Leave blank to keep current password') }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900 @if(!$isEdit) required @endif">
                                        {{ __('Confirm Password') }}
                                    </label>
                                    <div class="mt-2">
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                            @if(!$isEdit) required @endif
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                            placeholder="{{ __('Confirm password') }}">
                                        <div class="validation-error" id="password_confirmation-error">
                                            {{ __('Passwords do not match') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Profile Image -->
                            <h3 class="text-lg font-medium text-gray-700 mb-3">{{ __('Profile Image') }}</h3>
                            <div class="mb-2">
                                <label for="image" class="block text-sm font-medium leading-6 text-gray-900">{{ __('Profile Picture') }}</label>
                                <div class="mt-2 rounded-md shadow-sm">
                                    <input type="file" name="image" id="image" accept="image/*"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    
                                    <div class="mt-2 text-xs text-gray-500 bg-blue-50 p-2 rounded-md">
                                        <p class="font-medium text-blue-700">📐 Preferred aspect ratio: 1:1 (Square)</p>
                                        <p>Recommended size: 400x400 pixels minimum</p>
                                    </div>
                                    
                                    @if ($isEdit && isset($user->profile_picture))
                                        <div class="mt-2">
                                            <img src="{{ getImage($user->profile_picture, 'profile/') }}" alt="{{ __('user image') }}"
                                                class="rounded-md border border-gray-200 object-cover" 
                                                style="width: 80px; aspect-ratio: 1 / 1;">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Address Information -->
                            <h3 class="text-lg font-medium text-gray-700 mb-3">{{ __('Address Information') }}</h3>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="address[country_id]" class="block text-sm font-medium text-gray-700">
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
                                    <label for="address[city]" class="block text-sm font-medium leading-6 text-gray-900">{{ __('City') }}</label>
                                    <div class="mt-2 rounded-md shadow-sm">
                                        <input type="text" name="address[city]" id="city" required
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                            placeholder="{{ __('Eg. Kathmandu') }}" value="{{ $user->primaryAddress?->city }}">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="address[address_line_1]" class="block text-sm font-medium leading-6 text-gray-900">{{ __('Address Line 1') }}</label>
                                    <div class="mt-2 rounded-md shadow-sm">
                                        <input type="text" value="{{ $user->primaryAddress?->address_line_1 }}"
                                            name="address[address_line_1]" id="address_line_1"
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                            placeholder="{{ __('Street address') }}">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="address[address_line_2]" class="block text-sm font-medium leading-6 text-gray-900">{{ __('Address Line 2') }}</label>
                                    <div class="mt-2 rounded-md shadow-sm">
                                        <input type="text" name="address[address_line_2]"
                                            value="{{ $user->primaryAddress?->address_line_2 }}" id="address_line_2"
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                            placeholder="{{ __('Apartment, suite, unit, etc.') }}">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="mb-4">
                                        <label for="address[postal_code]" class="block text-sm font-medium leading-6 text-gray-900">{{ __('Postal Code') }}</label>
                                        <div class="mt-2 rounded-md shadow-sm">
                                            <input type="text" name="address[postal_code]" value="{{ $user->primaryAddress?->postal_code }}"
                                                id="postal_code"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                placeholder="{{ __('Eg. 1234') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="mb-2">
                                <label for="active" class="block text-sm font-medium leading-6 text-gray-900">{{ __('Status') }}</label>
                                <div class="mt-2 rounded-md shadow-sm">
                                    <select name="is_active" id="active"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option value="1" @if ($user->is_active) selected @endif>{{ __('Active') }}</option>
                                        <option value="0" @if (!$user->is_active) selected @endif>{{ __('Inactive') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Education Tab -->
                    <div id="education" class="tab-pane hidden">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
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
                                                <label class="block text-sm font-medium text-gray-700">{{ __('Type') }}</label>
                                                <select name="education[{{ $index }}][type]"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                    @foreach (['under_slc' => __('Under SLC'), 'slc' => __('SLC'), 'plus_two' => __('+2'), 'bachelors' => __('Bachelors'), 'masters' => __('Masters'), 'phd' => __('PhD'), 'training' => __('Training')] as $value => $label)
                                                        <option value="{{ $value }}"
                                                            @if ($education->type === $value) selected @endif>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700">{{ __('Degree/Course') }}</label>
                                                <input type="text" name="education[{{ $index }}][degree]"
                                                    value="{{ $education->degree }}"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>

                                            <div class="col-span-2">
                                                <label
                                                    class="block text-sm font-medium text-gray-700">{{ __('Institution') }}</label>
                                                <input type="text" name="education[{{ $index }}][institution]"
                                                    value="{{ $education->institution }}"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700">{{ __('Start Date') }}</label>
                                                <input type="date" name="education[{{ $index }}][start_date]"
                                                    value="{{ $education->start_date ? $education->start_date->format('Y-m-d') : '' }}"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700">{{ __('End Date') }}</label>
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
                    </div>

                    <!-- Work Experience Tab -->
                    <div id="experience" class="tab-pane hidden">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
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
                                                <label
                                                    class="block text-sm font-medium text-gray-700">{{ __('Job Title') }}</label>
                                                <input type="text" name="experience[{{ $index }}][job_title]"
                                                    value="{{ $experience->job_title }}"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>

                                            <div class="col-span-2">
                                                <label class="block text-sm font-medium text-gray-700">{{ __('Company') }}</label>
                                                <input type="text" name="experience[{{ $index }}][company]"
                                                    value="{{ $experience->company }}"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700">{{ __('Start Date') }}</label>
                                                <input type="date" name="experience[{{ $index }}][start_date]"
                                                    value="{{ old("experience.{$index}.start_date", optional($experience->start_date)->format('Y-m-d')) }}"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700">{{ __('End Date') }}</label>
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
                    </div>

                    <!-- Preferences Tab -->
                    <div id="preferences" class="tab-pane hidden">
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('Preferences') }}</h3>
                            <div>
                                <label for="preferences[countries][]"
                                    class="block text-sm font-medium text-gray-700">{{ __('Preferred Countries') }}</label>
                                <select id="preferred_countries" name="preferences[countries][]"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    multiple>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}"
                                            @if (isset($user->preference) && in_array($country->id, $user->preference->countries ?? [])) selected @endif>{{ $country->name }}</option>
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
                </div>

                <div class="flex justify-end w-full pt-4 border-t border-gray-200">
                    <div>
                        <button type="submit"
                            class="inline-block w-full px-8 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">{{ __('Save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('css')
<style>
    .required::after {
        content: "*";
        color: #e53e3e;
        margin-left: 2px;
    }
    
    .validation-error {
        color: #e53e3e;
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: none;
    }

    .error-border {
        border-color: #e53e3e !important;
    }

    .error-shake {
        animation: shake 0.5s;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
</style>
@endpush

@push('js')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @include('modules.shared.state_prefill', ['entity' => $user, 'countries' => $countries])
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Tab switching functionality
        document.querySelectorAll('[data-tab]').forEach(button => {
            button.addEventListener('click', () => {
                // Hide all tab panes
                document.querySelectorAll('.tab-pane').forEach(tab => tab.classList.add('hidden'));
                // Show selected tab pane
                document.querySelector(`#${button.dataset.tab}`).classList.remove('hidden');
                // Update tab button styles
                document.querySelectorAll('[data-tab]').forEach(btn => {
                    btn.classList.remove('border-indigo-600', 'text-indigo-600');
                    btn.classList.add('border-transparent');
                });
                button.classList.add('border-indigo-600', 'text-indigo-600');
                button.classList.remove('border-transparent');
            });
        });

        // Existing Select2 initialization
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
                <label class="block text-sm font-medium text-gray-700">Type</label>
                <select name="education[${index}][type]" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="under_slc">{{ __('Under SLC') }}</option>
                    <option value="slc">{{ __('SLC') }}</option>
                    <option value="plus_two">{{ __('+2') }}</option>
                    <option value="bachelors">{{ __('Bachelors') }}</option>
                    <option value="masters">{{ __('Masters') }}</option>
                    <option value="phd">{{ __('PhD') }}</option>
                    <option value="training">{{ __('Training') }}</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Degree/Course') }}</label>
                <input type="text" name="education[${index}][degree]" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700">{{ __('Institution') }}</label>
                <input type="text" name="education[${index}][institution]" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Start Date') }}</label>
                <input type="date" name="education[${index}][start_date]" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
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
                <label class="block text-sm font-medium text-gray-700">{{ __('Job Title') }}</label>
                <input type="text" name="experience[${index}][job_title]" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700">{{ __('Company') }}</label>
                <input type="text" name="experience[${index}][company]" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Start Date') }}</label>
                <input type="date" name="experience[${index}][start_date]" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
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

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('userForm');
            
            // Validation rules for each field
            const validationRules = {
                role: {
                    required: true,
                    message: 'Role is required'
                },
                first_name: {
                    required: true,
                    message: 'First name is required'
                },
                last_name: {
                    required: true,
                    message: 'Last name is required'
                },
                email: {
                    required: true,
                    pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                    message: 'Valid email address is required'
                },
                phone: {
                    required: true,
                    minLength: 6,
                    maxLength: 15,
                    pattern: /^\d+$/,
                    message: 'Phone number must be between 6 and 15 digits'
                }
            };

            // Validate single field
            function validateField(field) {
                const rules = validationRules[field.id];
                if (!rules) return true;

                let isValid = true;
                const errorElement = document.getElementById(`${field.id}-error`);
                
                // Required check
                if (rules.required && !field.value.trim()) {
                    isValid = false;
                }
                
                // Pattern check
                if (rules.pattern && field.value.trim() && !rules.pattern.test(field.value.trim())) {
                    isValid = false;
                }
                
                // Length checks
                if (rules.minLength && field.value.trim().length < rules.minLength) {
                    isValid = false;
                }
                if (rules.maxLength && field.value.trim().length > rules.maxLength) {
                    isValid = false;
                }

                // Show/hide error
                if (errorElement) {
                    errorElement.style.display = isValid ? 'none' : 'block';
                    field.classList.toggle('error-border', !isValid);
                }

                return isValid;
            }

            // Validate all fields
            function validateForm() {
                let isValid = true;
                let firstError = null;

                // Reset previous errors
                document.querySelectorAll('.validation-error').forEach(el => {
                    el.style.display = 'none';
                });
                document.querySelectorAll('.error-border').forEach(el => {
                    el.classList.remove('error-border');
                });

                // Validate each field
                Object.keys(validationRules).forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (field) {
                        const fieldValid = validateField(field);
                        if (!fieldValid && !firstError) {
                            firstError = field;
                        }
                        isValid = isValid && fieldValid;
                    }
                });

                // Scroll to first error
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.classList.add('error-shake');
                    setTimeout(() => firstError.classList.remove('error-shake'), 500);
                }

                return isValid;
            }

            // Form submission handler
            form.addEventListener('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                }
            });

            // Real-time validation
            Object.keys(validationRules).forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.addEventListener('blur', () => validateField(field));
                    field.addEventListener('input', () => {
                        field.classList.remove('error-border');
                        document.getElementById(`${fieldId}-error`).style.display = 'none';
                    });
                }
            });
        });
    </script>
@endpush
