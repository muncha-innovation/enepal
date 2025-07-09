{{-- General Information Section --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 required">{{ __('Business Name') }}</label>
        <input type="text" name="name" id="name" value="{{ old('name', $business->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
        <div data-error-for="name" class="validation-error"></div>
    </div>
    <div>
        <label for="type_id" class="block text-sm font-medium text-gray-700 required">{{ __('Business Type') }}</label>
        <select name="type_id" id="type_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
            @foreach($businessTypes as $type)
                <option value="{{ $type->id }}" {{ (old('type_id', $business->type_id) == $type->id) ? 'selected' : '' }}>{{ $type->title }}</option>
            @endforeach
        </select>
        <div data-error-for="type_id" class="validation-error"></div>
    </div>
</div>

{{-- Description in multiple languages --}}
@foreach (config('app.supported_locales') as $locale)
    <div class="mb-2">
        <label for="description[{{ $locale }}]"
            class="block text-sm font-medium text-gray-700">{{ __('description') }} ({{ strtoupper($locale) }})</label>
        <textarea rows="3" id="description[{{ $locale }}]" name="description[{{ $locale }}]" type="text"
            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ $business->getTranslation('description', $locale, false) ?? old("description.$locale") }}</textarea>
    </div>
@endforeach

{{-- Logo Upload --}}
<div class="mb-4">
    <label for="logo" class="block text-sm font-medium leading-6 text-gray-900 {{ !isset($business->logo) ? 'required' : '' }}">{{ __('business.logo') }}</label>
    <input type="file" {{ !isset($business->logo) ? 'required' : '' }} name="logo" id="logo"
        accept="image/*"
        class="cursor-pointer block w-full mt-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-md file:bg-gray-200 file:text-gray-700 file:text-sm file:px-4 file:border-none file:py-2 focus:border-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-40" />
    <div class="validation-error" id="logo-error">{{ __('Logo image is required') }}</div>
    @if (isset($business->logo))
        <img src="{{ getImage($business->logo, 'business/logo/') }}" alt="logo"
            class="w-20 h-20 mt-2">
    @endif
</div>

{{-- Cover Image Upload --}}
<div class="mb-4">
    <label for="cover_image" class="block text-sm font-medium leading-6 text-gray-900 {{ !isset($business->cover_image) ? 'required' : '' }}">{{ __('business.cover_image') }}</label>
    <input type="file" {{ !isset($business->cover_image) ? 'required' : '' }} name="cover_image"
        id="cover_image" accept="image/*"
        class="cursor-pointer block w-full mt-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-md file:bg-gray-200 file:text-gray-700 file:text-sm file:px-4 file:border-none file:py-2 focus:border-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-40" />
    <div class="validation-error" id="cover_image-error">{{ __('Cover image is required') }}</div>
    @if (isset($business->cover_image))
        <img src="{{ getImage($business->cover_image, 'business/cover_image/') }}" alt="cover_image"
            class="w-20 h-20 mt-2">
    @endif
</div>

{{-- Established Year --}}
<div class="mb-2">
    <label for="established_year" class="block text-sm font-medium leading-6 text-gray-900">{{ __('business.established_year') }}</label>
    <input type="number" name="established_year" id="established_year" 
        value="{{ $business->established_year ?? old('established_year') }}"
        min="1900" max="{{ date('Y') }}"
        class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
        placeholder="{{ __('e.g. 2010') }}">
</div>

{{-- Custom Email Message --}}
<div class="mb-2">
    <label for="custom_email_message"
        class="block text-sm font-medium leading-6 text-gray-900">{{ __('business.custom_email_message') }}</label>
    <textarea name="custom_email_message" id="custom_email_message" 
        class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" 
        placeholder="{{ __('This message will be sent to the member in the email') }}">{{ $business->custom_email_message ?? old('custom_email_message') }}</textarea>
</div>

{{-- Status --}}
<div class="mb-2">
    <label for="active"
        class="block text-sm font-medium leading-6 text-gray-900">{{ __('business.status') }}</label>
    <div class="mt-2 rounded-md shadow-sm">
        <select name="is_active" id="active"
            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            <option value="1" @if (isset($business->is_active) && $business->is_active) selected @endif>{{ __('business.active') }}</option>
            <option value="0" @if (isset($business->is_active) && !$business->is_active) selected @endif>{{ __('business.inactive') }}</option>
        </select>
    </div>
</div>

{{-- Max Notifications --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="mb-2">
        <label for="max_notifications_per_day" 
            class="block text-sm font-medium leading-6 text-gray-900">{{ __('Max Notifications Per Day') }}</label>
        <input type="number" name="max_notifications_per_day" id="max_notifications_per_day" 
            value="{{ $business->max_notifications_per_day ?? old('max_notifications_per_day') ?? 5 }}"
            min="0" max="100"
            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
    </div>
    
    <div class="mb-2">
        <label for="max_notifications_per_month" 
            class="block text-sm font-medium leading-6 text-gray-900">{{ __('Max Notifications Per Month') }}</label>
        <input type="number" name="max_notifications_per_month" id="max_notifications_per_month" 
            value="{{ $business->max_notifications_per_month ?? old('max_notifications_per_month') ?? 30 }}"
            min="0" max="500"
            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
    </div>
</div>

<script>
function toggleManpowerTab(typeId) {
    const educationBusinessTypes = ['5', '6']; // IDs for manpower and education consultancy types
    const showEducationTab = educationBusinessTypes.includes(typeId);
    
    const tabContainer = document.getElementById('manpower-consultancy-tab-container');
    if (tabContainer) {
        tabContainer.style.display = showEducationTab ? '' : 'none';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type_id');
    if (typeSelect) {
        toggleManpowerTab(typeSelect.value);
    }
});
</script>