{{-- Manpower & Education Consultancy Fields --}}
@include('modules.business.components.education_fields', [
    'showEducationFields' => true,
    'business' => $business,
    'languages' => $languages,
    'countries' => $countries
])

<div id="languages-container" class="space-y-4">
    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Languages Taught') }}</h3>
    @foreach($business->taughtLanguages as $index => $language)
        <div class="flex items-center space-x-4">
            <div class="w-1/3">
                <select name="languages[{{ $index }}][id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @foreach($languages as $lang)
                        <option value="{{ $lang->id }}" {{ ($language->id == $lang->id) ? 'selected' : '' }}>{{ $lang->name }}</option>
                    @endforeach
                </select>
                <div data-error-for="languages.{{ $index }}.id" class="validation-error"></div>
            </div>
            <div class="w-1/3">
                <input type="number" name="languages[{{ $index }}][price]" value="{{ old('languages.'.$index.'.price', $language->pivot->price ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Price">
                <div data-error-for="languages.{{ $index }}.price" class="validation-error"></div>
            </div>
            <div class="w-1/3">
                <input type="text" name="languages[{{ $index }}][currency]" value="{{ old('languages.'.$index.'.currency', $language->pivot->currency ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Currency (e.g. USD)">
                <div data-error-for="languages.{{ $index }}.currency" class="validation-error"></div>
            </div>
        </div>
    @endforeach
    <button type="button" id="add-language" class="text-sm text-blue-600 hover:underline">{{ __('+ Add Language') }}</button>
</div>

<div id="destinations-container" class="space-y-4 mt-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Destinations') }}</h3>
    @foreach($business->destinations as $index => $destination)
        <div class="flex items-center space-x-4">
            <div class="w-1/2">
                <select name="destinations[{{ $index }}][country_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ ($destination->id == $country->id) ? 'selected' : '' }}>{{ $country->name }}</option>
                    @endforeach
                </select>
                <div data-error-for="destinations.{{ $index }}.country_id" class="validation-error"></div>
            </div>
            <div class="w-1/2">
                <input type="number" name="destinations[{{ $index }}][num_people_sent]" value="{{ old('destinations.'.$index.'.num_people_sent', $destination->pivot->num_people_sent ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Number of people sent">
                <div data-error-for="destinations.{{ $index }}.num_people_sent" class="validation-error"></div>
            </div>
        </div>
    @endforeach
    <button type="button" id="add-destination" class="text-sm text-blue-600 hover:underline">{{ __('+ Add Destination') }}</button>
</div>