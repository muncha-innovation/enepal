<div class="destination-row bg-gray-50 p-4 rounded-lg relative">
    <button type="button" class="remove-entry absolute top-2 right-2 text-red-600 hover:text-red-800">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Country') }}</label>
            <select name="destinations[{{ $index }}][country_id]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @foreach($countries as $country)
                    <option value="{{ $country->id }}" @if(isset($selectedCountry) && $selectedCountry->country_id == $country->id) selected @endif>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Number of People Sent') }}</label>
            <input type="number" name="destinations[{{ $index }}][num_people_sent]" 
                value="{{ $selectedCountry->pivot->num_people_sent ?? '' }}"
                min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>
    </div>
</div>
