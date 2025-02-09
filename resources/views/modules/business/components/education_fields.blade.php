<div class="education-fields" style="display: {{ $showEducationFields ? 'block' : 'none' }}">
    <!-- Language Teaching Section -->
    <div class="mb-4">
        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Languages Taught') }}</h3>
        <div id="languages-container">
            @if(isset($business) && $business->taughtLanguages->isNotEmpty())
                @foreach($business->taughtLanguages as $language)
                    @include('modules.business.components.language_row', [
                        'languages' => $languages,
                        'index' => $loop->index,
                        'selectedLanguage' => $language,
                        'price' => $language->pivot->price,
                        'numPeopleTaught' => $language->pivot->num_people_taught,
                        'selectedLevel' => $language->pivot->level
                    ])
                @endforeach
            @endif
        </div>
        <button type="button" id="add-language" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded-md">
            {{ __('Add Language') }}
        </button>
    </div>

    <!-- Destinations Section -->
    <div class="mb-4">
        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Destinations') }}</h3>
        <div id="destinations-container">
            @if(isset($business) && $business->destinations->isNotEmpty())
                @foreach($business->destinations as $destination)
                    @include('modules.business.components.destination_row', [
                        'countries' => $countries,
                        'index' => $loop->index,
                        'selectedCountry' => $destination,
                        'numPeopleSent' => $destination->pivot->num_people_sent
                    ])
                @endforeach
            @endif
        </div>
        <button type="button" id="add-destination" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded-md">
            {{ __('Add Destination') }}
        </button>
    </div>
</div>
