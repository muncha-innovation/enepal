<div class="destination-entry grid grid-cols-2 gap-4 mb-2">
    <select name="destinations[{{ $index }}][country_id]" class="rounded-md border-gray-300">
        @foreach($countries as $country)
            <option value="{{ $country->id }}" {{ isset($selectedCountry) && $selectedCountry->id == $country->id ? 'selected' : '' }}>
                {{ $country->name }}
            </option>
        @endforeach
    </select>
    <input type="number" name="destinations[{{ $index }}][num_people_sent]" 
        value="{{ $numPeopleSent ?? '' }}" 
        class="rounded-md border-gray-300" 
        placeholder="Number of people sent">
</div>
