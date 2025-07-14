@extends('layouts.app')

@section('content')
    {{-- if any errors display errrors --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ $errors->first() }}</span>
        </div>
    @endif
    <h1 class="text-2xl font-semibold text-gray-700 mb-4">{{ __('Profile') }}</h1>

    <!-- Tab Navigation -->
    <div class="border-b border-gray-200 mb-6">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="profileTabs" role="tablist">
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 {{ (!session('active_profile_tab') || session('active_profile_tab') == 'general') ? 'border-indigo-600 text-indigo-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}" 
                    id="general-tab" data-tab="general" type="button" role="tab" aria-selected="{{ (!session('active_profile_tab') || session('active_profile_tab') == 'general') ? 'true' : 'false' }}">
                    {{ __('General') }}
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 {{ session('active_profile_tab') == 'security' ? 'border-indigo-600 text-indigo-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}" 
                    id="security-tab" data-tab="security" type="button" role="tab" aria-selected="{{ session('active_profile_tab') == 'security' ? 'true' : 'false' }}">
                    {{ __('Security') }}
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 {{ session('active_profile_tab') == 'work-experience' ? 'border-indigo-600 text-indigo-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}" 
                    id="work-experience-tab" data-tab="work-experience" type="button" role="tab" aria-selected="{{ session('active_profile_tab') == 'work-experience' ? 'true' : 'false' }}">
                    {{ __('Work Experience') }}
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 {{ session('active_profile_tab') == 'education' ? 'border-indigo-600 text-indigo-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}" 
                    id="education-tab" data-tab="education" type="button" role="tab" aria-selected="{{ session('active_profile_tab') == 'education' ? 'true' : 'false' }}">
                    {{ __('Education') }}
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 {{ session('active_profile_tab') == 'preferences' ? 'border-indigo-600 text-indigo-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}" 
                    id="preferences-tab" data-tab="preferences" type="button" role="tab" aria-selected="{{ session('active_profile_tab') == 'preferences' ? 'true' : 'false' }}">
                    {{ __('Preferences') }}
                </button>
            </li>
        </ul>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- General Tab -->
        <div id="general" class="tab-pane {{ (!session('active_profile_tab') || session('active_profile_tab') == 'general') ? 'active' : 'hidden' }}">
            @include('modules.profile.partials.general')
        </div>

        <!-- Security Tab -->
        <div id="security" class="tab-pane {{ session('active_profile_tab') == 'security' ? 'active' : 'hidden' }}">
            @include('modules.profile.partials.security')
        </div>

        <!-- Work Experience Tab -->
        <div id="work-experience" class="tab-pane {{ session('active_profile_tab') == 'work-experience' ? 'active' : 'hidden' }}">
            @include('modules.profile.partials.work-experience')
        </div>

        <!-- Education Tab -->
        <div id="education" class="tab-pane {{ session('active_profile_tab') == 'education' ? 'active' : 'hidden' }}">
            @include('modules.profile.partials.education')
        </div>

        <!-- Preferences Tab -->
        <div id="preferences" class="tab-pane {{ session('active_profile_tab') == 'preferences' ? 'active' : 'hidden' }}">
            @include('modules.profile.partials.preferences')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Global date formatting function
        window.formatDate = function(dateString) {
            if (!dateString) return '{{ __("Present") }}';
            
            const date = new Date(dateString);
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        };
        
        document.querySelectorAll('[data-tab]').forEach(button => {
            button.addEventListener('click', () => {
                document.querySelectorAll('.tab-pane').forEach(tab => tab.classList.add('hidden'));
                document.querySelector(`#${button.dataset.tab}`).classList.remove('hidden');
                document.querySelectorAll('[data-tab]').forEach(btn => btn.classList.remove('border-indigo-600', 'text-indigo-600'));
                button.classList.add('border-indigo-600', 'text-indigo-600');
                
                // Load tab-specific data if needed - this is the only place we should call these functions
                if (button.id === 'work-experience-tab') {
                    if (typeof window.fetchWorkExperience === 'function') {
                        window.fetchWorkExperience();
                    }
                } else if (button.id === 'education-tab') {
                    if (typeof window.fetchEducation === 'function') {
                        window.fetchEducation();
                    }
                }
            });
        });

        // File upload handling is now done in general.blade.php partial

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
            
            if (!document.getElementById('state').value) {
                errors['state'] = '{{ __("Please select a state") }}';
                hasErrors = true;
            }
            
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

            // Validate password confirmation if password is entered
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;
            if (password && password !== passwordConfirmation) {
                errors['password_confirmation'] = '{{ __("Passwords do not match") }}';
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

        // Remove these duplicate functions as they're now properly handled in the partials
        // function fetchWorkExperience() { ... }
        // function fetchEducation() { ... }
        // function deleteExperience(id) { ... }
        // function deleteEducation(id) { ... }
    </script>
    @include('modules.shared.state_prefill', ['entity' => $user, 'countries' => $countries])
@endsection
