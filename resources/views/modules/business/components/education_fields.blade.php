<div class="education-fields" style="display: {{ $showEducationFields ? 'block' : 'none' }}">
    <div class="border-t border-gray-200 pt-4 mt-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('Languages Taught') }}</h3>
            <button type="button" id="add-language" 
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('Add Language') }}
            </button>
        </div>
        
        <div id="languages-container" class="space-y-4">
            @if(isset($business) && isset($business->taughtLanguages) && $business->taughtLanguages->isNotEmpty())
                @foreach($business->taughtLanguages as $index => $language)
                    @include('modules.business.components.language_row', [
                        'index' => $index,
                        'language' => $language,
                        'languages' => $languages
                    ])
                @endforeach
            @elseif(isset($business) && isset($business->languages) && $business->languages->isNotEmpty())
                @foreach($business->languages as $index => $language)
                    @include('modules.business.components.language_row', [
                        'index' => $index,
                        'language' => $language,
                        'languages' => $languages
                    ])
                @endforeach
            @else
                <p class="text-gray-500 text-center py-4" id="no-languages-message">{{ __('No languages added yet') }}</p>
            @endif
        </div>
    </div>

    <!-- Destinations Section -->
    <div class="border-t border-gray-200 pt-4 mt-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('Destinations') }}</h3>
            <button type="button" id="add-destination" 
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('Add Destination') }}
            </button>
        </div>
        <div id="destinations-container" class="space-y-4">
            @if(isset($business) && isset($business->destinations) && $business->destinations->isNotEmpty())
                @foreach($business->destinations as $destination)
                    @include('modules.business.components.destination_row', [
                        'countries' => $countries,
                        'index' => $loop->index,
                        'selectedCountry' => $destination,
                    ])
                @endforeach
            @else
                <p class="text-gray-500 text-center py-4" id="no-destinations-message">{{ __('No destinations added yet') }}</p>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add language button handler
            document.getElementById('add-language')?.addEventListener('click', function() {
                const container = document.getElementById('languages-container');
                const noLanguagesMessage = document.getElementById('no-languages-message');
                if (noLanguagesMessage) {
                    noLanguagesMessage.remove();
                }
                
                // Get the next index based on existing items
                const index = document.querySelectorAll('.language-row').length;
                
                // Fetch the language row template via AJAX
                fetch(`/business/language-row/${index}`)
                    .then(response => response.text())
                    .then(html => {
                        // Append the new row to the container
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = html;
                        container.appendChild(tempDiv.firstChild);
                    })
                    .catch(error => console.error('Error loading language component:', error));
            });
            
            // Add destination button handler
            document.getElementById('add-destination')?.addEventListener('click', function() {
                const container = document.getElementById('destinations-container');
                const noDestinationsMessage = document.getElementById('no-destinations-message');
                if (noDestinationsMessage) {
                    noDestinationsMessage.remove();
                }
                
                // Get the next index based on existing items
                const index = document.querySelectorAll('.destination-row').length;
                
                // Fetch the destination row template via AJAX
                fetch(`/business/destination-row/${index}`)
                    .then(response => response.text())
                    .then(html => {
                        // Append the new row to the container
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = html;
                        container.appendChild(tempDiv.firstChild);
                    })
                    .catch(error => console.error('Error loading destination component:', error));
            });
        });

        // Add event listener for remove buttons
        document.addEventListener('click', function(e) {
            const removeButton = e.target.closest('.remove-entry');
            if (removeButton) {
                const row = removeButton.closest('.language-row, .destination-row');
                if (row) {
                    if (confirm('Are you sure you want to remove this entry?')) {
                        row.remove();
                        
                        // Show "no languages" message if no languages left
                        const languagesContainer = document.getElementById('languages-container');
                        if (languagesContainer && languagesContainer.querySelectorAll('.language-row').length === 0) {
                            languagesContainer.innerHTML = '<p class="text-gray-500 text-center py-4" id="no-languages-message">{{ __("No languages added yet") }}</p>';
                        }
                        
                        // Show "no destinations" message if no destinations left
                        const destinationsContainer = document.getElementById('destinations-container');
                        if (destinationsContainer && destinationsContainer.querySelectorAll('.destination-row').length === 0) {
                            destinationsContainer.innerHTML = '<p class="text-gray-500 text-center py-4" id="no-destinations-message">{{ __("No destinations added yet") }}</p>';
                        }
                    }
                }
            }
        });
    </script>
</div>
