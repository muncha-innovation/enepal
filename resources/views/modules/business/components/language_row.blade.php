<div class="language-entry grid grid-cols-5 gap-4 mb-2">
    <select name="languages[{{ $index }}][id]" class="rounded-md border-gray-300">
        @foreach ($languages as $lang)
            <option value="{{ $lang->id }}"
                {{ isset($selectedLanguage) && $selectedLanguage->id == $lang->id ? 'selected' : '' }}>
                {{ $lang->name }}
            </option>
        @endforeach
    </select>
    <select name="languages[{{ $index }}][currency]" class="rounded-md border-gray-300">
        <option value="NPR"
            {{ (isset($currency) && $currency == 'NPR' ? 'selected' : !isset($currency)) ? 'selected' : '' }}>NPR</option>
        <option value="USD" {{ isset($currency) && $currency == 'USD' ? 'selected' : '' }}>USD</option>

    </select>
    <input type="number" name="languages[{{ $index }}][price]" value="{{ $price ?? '' }}"
        class="rounded-md border-gray-300" placeholder="Price">

    {{-- <input type="number" name="languages[{{ $index }}][num_people_taught]" 
        value="{{ $numPeopleTaught ?? '' }}" 
        class="rounded-md border-gray-300" 
        placeholder="Number of students">
    <select name="languages[{{ $index }}][level]" class="rounded-md border-gray-300">
        @foreach (['beginner', 'intermediate', 'advanced'] as $level)
            <option value="{{ $level }}" {{ isset($selectedLevel) && $selectedLevel == $level ? 'selected' : '' }}>
                {{ ucfirst($level) }}
            </option>
        @endforeach
    </select> --}}
</div>
