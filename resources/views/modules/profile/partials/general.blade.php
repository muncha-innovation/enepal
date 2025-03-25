<section class="mb-4">
    <div class="bg-white p-4 shadow rounded flex gap-3 divide-x">
        <div class="col-span-full flex items-center gap-x-8">
            <img id="profile-picture" src="{{ getImage(auth()->user()->profile_picture, 'profile/') }}" alt=""
                class="h-24 w-24 flex-none rounded-lg bg-gray-200 object-cover">
            <div>
                <button type="button" id="file-selector"
                    class="file-selector rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">{{ __('Change avatar') }}</button>
                <p class="mt-2 text-xs leading-5 text-gray-400">{{ __('JPG, GIF or PNG. 1MB max.') }}</p>
            </div>
        </div>
        <div class="px-4">
            <h3 class="mb-2">{{ __('Image Requirements') }}</h3>
            <ul class="list-disc list-inside text-sm text-gray-600">
                <li>{{ __('Minimum 256x256 pixels') }}</li>
                <li>{{ __('Maximum 1MB') }}</li>
                <li>{{ __('Only JPG, GIF or PNG') }}</li>
            </ul>
        </div>
    </div>
</section>

<section>
    <div class="bg-white p-4 shadow rounded">
        <h2 class="font-semibold">{{ __('User Details') }}</h2>

        <form id="profileForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            @include('modules.shared.success_error')
            <input type="file" id="file-input" name="profile_picture" accept="image/*" style="display: none;">
            @if (auth()->user()->force_update_password)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 my-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M10 3a1 1 0 0 1 1 1v5a1 1 0 0 1-2 0V4a1 1 0 0 1 1-1zm0 8a1 1 0 0 1 1 1v1a1 1 0 0 1-2 0v-1a1 1 0 0 1 1-1zm0 6a1 1 0 0 1-1-1v-1a1 1 0 0 1 2 0v1a1 1 0 0 1-1 1z">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">{{ __('You are required to update your password') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (!auth()->user()->is_active)
                <div class="bg-red-50 border-l-4 border-red-400 p-4 my-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M10 3a1 1 0 0 1 1 1v5a1 1 0 0 1-2 0V4a1 1 0 0 1 1-1zm0 8a1 1 0 0 1 1 1v1a1 1 0 0 1-2 0v-1a1 1 0 0 1 1-1zm0 6a1 1 0 0 1-1-1v-1a1 1 0 0 1 2 0v1a1 1 0 0 1-1 1z">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ __('Your account is inactive. Please contact admin') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Personal Information -->
            <div class="mb-2">
                <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900">
                    {{ __('First Name') }} <span class="text-red-500">*</span>
                </label>
                <div class="mt-2 rounded-md shadow-sm">
                    <input type="text" name="first_name" id="first_name" placeholder="Eg. John"
                        value="{{ $user->first_name }}"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
                <p class="mt-1 text-sm text-red-600 hidden" id="first_name-error"></p>
            </div>
            
            <div class="mb-2">
                <label for="last_name" class="block text-sm font-medium leading-6 text-gray-900">
                    {{ __('Last Name') }} <span class="text-red-500">*</span>
                </label>
                <div class="mt-2 rounded-md shadow-sm">
                    <input type="text" name="last_name" id="last_name" placeholder="Eg. Doe"
                        value="{{ $user->last_name }}"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
                <p class="mt-1 text-sm text-red-600 hidden" id="last_name-error"></p>
            </div>
            
            <!-- Address Information -->
            <h3 class="text-lg font-medium text-gray-700 mt-6 mb-3">{{ __('Address Information') }}</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="address[country_id]" class="block text-sm font-medium text-gray-700 required">
                        {{ __('Country') }} <span class="text-red-500">*</span></label>
                    <div class="mt-1">
                        <select id="country" name="address[country_id]" required
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">{{ __('Select Country') }}</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}" @if ($country->id == $user->primaryAddress?->country_id) selected @endif>
                                    {{ $country->name }} ({{ $country->dial_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <p class="mt-1 text-sm text-red-600 hidden" id="country-error"></p>
                </div>
                <div>
                    <label for="address[state_id]" class="block text-sm font-medium text-gray-700">
                        {{ __('State/Region') }}</label>
                    <div class="mt-1">
                        <select id="state" name="address[state_id]"
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">{{ __('Select State') }}</option>
                            @if (isset($user->primaryAddress?->state_id))
                                <option value="{{ $user->primaryAddress?->state_id }}" selected>
                                    {{ $user->primaryAddress?->state->name }}
                                </option>
                            @endif
                        </select>
                    </div>
                    <p class="mt-1 text-sm text-red-600 hidden" id="state-error"></p>
                </div>
            </div>

            <div class="mb-4">
                <label for="address[city]" class="block text-sm font-medium leading-6 text-gray-900 required">
                    {{ __('City') }} <span class="text-red-500">*</span>
                </label>
                <div class="mt-2 rounded-md shadow-sm">
                    <input type="text" name="address[city]" id="city" required
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        placeholder="Eg. Kathmandu" value="{{ $user->primaryAddress?->city }}">
                </div>
                <p class="mt-1 text-sm text-red-600 hidden" id="city-error"></p>
            </div>

            <div class="mb-4">
                <label for="address[address_line_1]" class="block text-sm font-medium leading-6 text-gray-900">{{ __('Address Line 1') }}</label>
                <div class="mt-2 rounded-md shadow-sm">
                    <input type="text" value="{{ $user->primaryAddress?->address_line_1 }}"
                        name="address[address_line_1]" id="address_line_1"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        placeholder="Street address">
                </div>
            </div>

            <div class="mb-4">
                <label for="address[address_line_2]" class="block text-sm font-medium leading-6 text-gray-900">{{ __('Address Line 2') }}</label>
                <div class="mt-2 rounded-md shadow-sm">
                    <input type="text" name="address[address_line_2]"
                        value="{{ $user->primaryAddress?->address_line_2 }}" id="address_line_2"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        placeholder="Apartment, suite, unit, etc.">
                </div>
            </div>

            <div class="mb-4">
                <label for="address[postal_code]" class="block text-sm font-medium leading-6 text-gray-900">{{ __('Postal Code') }}</label>
                <div class="mt-2 rounded-md shadow-sm">
                    <input type="text" name="address[postal_code]" value="{{ $user->primaryAddress?->postal_code }}"
                        id="postal_code"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        placeholder="Eg. 1234">
                </div>
            </div>

            <!-- Contact Information -->
            <h3 class="text-lg font-medium text-gray-700 mt-6 mb-3">{{ __('Contact Information') }}</h3>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
                    {{ __('Email') }} <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email"
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    placeholder="Eg. abc@gmail.com" value="{{ $user->email }}">
                <p class="mt-1 text-sm text-red-600 hidden" id="email-error"></p>
            </div>
            
            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-gray-700">
                    {{ __('Phone Number') }} <span class="text-red-500">*</span></label>
                <div class="mt-1">
                    <input id="phone" name="phone" type="text" value="{{ $user->phone }}" required
                        minLength="6" maxLength="15"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <p class="mt-1 text-sm text-red-600 hidden" id="phone-error"></p>
            </div>

            <div class="flex justify-end w-full mt-4">
                <div>
                    <button type="submit"
                        class="inline-block w-full px-8 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Save') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
    document.getElementById('file-selector').addEventListener('click', function() {
        document.getElementById('file-input').click();
    });
    
    document.getElementById('file-input').addEventListener('change', function() {
        const fileInput = this;
        const selectedImage = document.getElementById('profile-picture');

        if (fileInput.files && fileInput.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                selectedImage.setAttribute('src', e.target.result);
            };

            reader.readAsDataURL(fileInput.files[0]);
        }
    });

    // Client-side validation
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Reset all error messages
        document.querySelectorAll('.text-red-600').forEach(el => el.classList.add('hidden'));
        
        let hasErrors = false;
        const errors = {};

        // Validate required fields
        const requiredFields = ['first_name', 'last_name', 'email', 'phone'];
        requiredFields.forEach(field => {
            const element = document.getElementById(field);
            if (element && element.value.trim() === '') {
                errors[field] = '{{ __("This field is required") }}';
                hasErrors = true;
            }
        });
        
        // Validate select fields
        if (!document.getElementById('country').value) {
            errors['country'] = '{{ __("Please select a country") }}';
            hasErrors = true;
        }
        
        // Validate city field
        if (!document.getElementById('city').value) {
            errors['city'] = '{{ __("City is required") }}';
            hasErrors = true;
        }

        // Validate email format if not empty
        const email = document.getElementById('email').value.trim();
        if (email === '') {
            errors['email'] = '{{ __("Email is required") }}';
            hasErrors = true;
        } else if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            errors['email'] = '{{ __("Please enter a valid email address") }}';
            hasErrors = true;
        }

        // Phone validation 
        const phone = document.getElementById('phone').value.trim();
        if (phone === '') {
            errors['phone'] = '{{ __("Phone number is required") }}';
            hasErrors = true;
        } else if (!phone.match(/^\+?[\d\s-]{6,15}$/)) {
            errors['phone'] = '{{ __("Please enter a valid phone number") }}';
            hasErrors = true;
        }

        // Display errors if any
        if (hasErrors) {
            Object.keys(errors).forEach(field => {
                const errorElement = document.getElementById(`${field}-error`);
                if (errorElement) {
                    errorElement.textContent = errors[field];
                    errorElement.classList.remove('hidden');
                }
            });
            return;
        }

        // If validation passes, submit the form
        this.submit();
    });
</script>
