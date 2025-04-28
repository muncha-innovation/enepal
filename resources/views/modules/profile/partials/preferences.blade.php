<div class="bg-white p-6 rounded-lg shadow">
    <div id="validation-errors" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
        <span class="block sm:inline" id="error-message"></span>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="preferencesForm" method="POST" action="{{ route('profile.preferences.update') }}" class="space-y-6">
        @csrf
        <!-- User Type -->
        <div class="mb-6">
            <label for="user_type" class="block text-sm font-medium text-gray-700 mb-1">{{ __('User Type') }}</label>
            <select id="user_type" name="user_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="" {{ $user->preference?->user_type ? '' : 'selected' }}>{{ __('Not Specified') }}</option>
                <option value="student" {{ $user->preference?->user_type === 'student' ? 'selected' : '' }}>{{ __('Student') }}</option>
                <option value="nrn" {{ $user->preference?->user_type === 'nrn' ? 'selected' : '' }}>{{ __('Non-Resident Nepali (NRN)') }}</option>
                <option value="job_seeker" {{ $user->preference?->user_type === 'job_seeker' ? 'selected' : '' }}>{{ __('Job Seeker (Foreign Employment)') }}</option>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- App Language -->
            <div>
                <label for="app_language" class="block text-sm font-medium text-gray-700 mb-1">{{ __('App Language') }}</label>
                <select id="app_language" name="app_language" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="en" {{ $user->preference?->app_language === 'en' ? 'selected' : '' }}>{{__('English')}}</option>
                    <option value="np" {{ $user->preference?->app_language === 'np' ? 'selected' : '' }}>{{ __('Nepali') }}</option>
                    <!-- Other languages can be loaded dynamically -->
                </select>
            </div>

            <div id="known_languages_container" class="{{ $user->preference?->user_type === 'nrn' ? 'hidden' : '' }}">
                <label for="known_languages" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Known Languages') }}</label>
                <select id="known_languages" name="known_languages[]" class="languages-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" multiple>
                    <!-- Will be populated with JavaScript -->
                </select>
            </div>

            <div id="study_field_container" class="{{ $user->preference?->user_type !== 'student' ? 'hidden' : '' }}">
                <label for="study_field" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Study Field') }}</label>
                <input type="text" id="study_field" name="study_field" value="{{ $user->preference?->study_field }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div id="departure_date_container" class="{{ $user->preference?->user_type === 'nrn' ? 'hidden' : '' }}">
                <label for="departure_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Departure Date') }}</label>
                <input type="date" id="departure_date" name="departure_date" 
                    value="{{ $user->preference?->departure_date?->format('Y-m-d') }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div id="countries_container" class="{{ $user->preference?->user_type === 'nrn' ? 'hidden' : '' }}">
                <label for="countries" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Destination Countries') }}</label>
                <select id="countries" name="countries[]" class="countries-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" multiple>
                    <!-- Will be populated with JavaScript -->
                </select>
            </div>

            <!-- Passport Information -->
            <div>
                <div class="flex items-center mb-4">
                    <input type="checkbox" id="has_passport" name="has_passport" value="1"
                        {{ $user->preference?->has_passport ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="has_passport" class="ml-2 text-sm font-medium text-gray-700">{{ __('Has Passport') }}</label>
                </div>
                <div id="passport_expiry_container" class="{{ !$user->preference?->has_passport ? 'hidden' : '' }}">
                    <label for="passport_expiry" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Passport Expiry Date') }}</label>
                    <input type="date" id="passport_expiry" name="passport_expiry" 
                        value="{{ $user->preference?->passport_expiry?->format('Y-m-d') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Notification Preferences -->
            <div class="space-y-4">
                <div class="flex items-center">
                    <input type="checkbox" id="receive_notifications" name="receive_notifications" value="1"
                        {{ $user->preference?->receive_notifications ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="receive_notifications" class="ml-2 text-sm font-medium text-gray-700">
                        {{ __('Receive Notifications') }}
                    </label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="show_personalized_content" name="show_personalized_content" value="1"
                        {{ $user->preference?->show_personalized_content ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="show_personalized_content" class="ml-2 text-sm font-medium text-gray-700">
                        {{ __('Show Personalized Content') }}
                    </label>
                </div>
            </div>

            <!-- Distance Unit Preference -->
            <div>
                <label for="distance_unit" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Distance Unit') }}</label>
                <div class="flex items-center space-x-4 mt-2">
                    <div class="flex items-center">
                        <input type="radio" id="distance_unit_km" name="distance_unit" value="km"
                            {{ $user->preference?->distance_unit !== 'miles' ? 'checked' : '' }}
                            class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="distance_unit_km" class="ml-2 text-sm font-medium text-gray-700">
                            {{ __('Kilometers (km)') }}
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="distance_unit_miles" name="distance_unit" value="miles"
                            {{ $user->preference?->distance_unit === 'miles' ? 'checked' : '' }}
                            class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="distance_unit_miles" class="ml-2 text-sm font-medium text-gray-700">
                            {{ __('Miles') }}
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150">
                {{ __('Save Preferences') }}
            </button>
        </div>
    </form>
</div>

<!-- Include Choices.js for searchable dropdowns -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hasPassportCheckbox = document.getElementById('has_passport');
    const passportExpiryContainer = document.getElementById('passport_expiry_container');
    const userTypeSelect = document.getElementById('user_type');
    const studyFieldContainer = document.getElementById('study_field_container');
    const departureDateContainer = document.getElementById('departure_date_container');
    const countriesContainer = document.getElementById('countries_container');
    const knownLanguagesContainer = document.getElementById('known_languages_container');
    const validationErrorsContainer = document.getElementById('validation-errors');
    const errorMessageElement = document.getElementById('error-message');
    
    // Initialize searchable dropdowns
    const languagesSelect = new Choices('#known_languages', {
        removeItemButton: true,
        searchEnabled: true,
        placeholder: true,
        placeholderValue: 'Select languages',
    });
    
    const countriesSelect = new Choices('#countries', {
        removeItemButton: true,
        searchEnabled: true,
        placeholder: true,
        placeholderValue: 'Select countries',
    });
    
    // Fetch languages from the server
    fetchLanguages();
    
    // Fetch countries from the server 
    fetchCountries();
    
    // Handle passport checkbox change
    hasPassportCheckbox?.addEventListener('change', function() {
        passportExpiryContainer.classList.toggle('hidden', !this.checked);
    });
    
    // Handle user type change
    userTypeSelect?.addEventListener('change', function() {
        const isStudent = this.value === 'student';
        const isNRN = this.value === 'nrn';
        
        // Show/hide study field for students
        studyFieldContainer.classList.toggle('hidden', !isStudent);
        
        // Show/hide departure date, countries and known languages for NRN
        departureDateContainer.classList.toggle('hidden', isNRN);
        countriesContainer.classList.toggle('hidden', isNRN);
        knownLanguagesContainer.classList.toggle('hidden', isNRN);
        
        if (isStudent) {
            studyFieldInput.focus();
        }
    });
    
    // Function to fetch languages
    async function fetchLanguages() {
        try {
            const response = await fetch('/api/languages');
            const data = await response.json();
            
            if (data && data.data) {
                const languages = data.data;
                
                // Get user's current selected languages
                const selectedLanguages = @json($user->preference?->known_languages ?? []);
                
                // Add options to select
                languagesSelect.clearChoices();
                languagesSelect.setChoices(languages.map(lang => ({
                    value: lang.code,
                    label: lang.name,
                    selected: selectedLanguages.includes(lang.code)
                })));
            }
        } catch (error) {
            console.error('Error fetching languages:', error);
        }
    }
    
    // Function to fetch countries
    async function fetchCountries() {
        try {
            const response = await fetch('/api/countries');
            const data = await response.json();
            
            // Handle different response formats
            const countries = Array.isArray(data) ? data : 
                             (data.countries || []);
            
            // Get user's current selected countries
            const selectedCountries = @json($user->preference?->countries ?? []);
            
            // Add options to select
            countriesSelect.clearChoices();
            countriesSelect.setChoices(countries.map(country => ({
                value: country.id,
                label: country.name,
                selected: selectedCountries.includes(country.id.toString())
            })));
        } catch (error) {
            console.error('Error fetching countries:', error);
        }
    }
});
</script>