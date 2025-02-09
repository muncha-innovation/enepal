<div class="language-row bg-gray-50 p-4 rounded-lg relative">
    <button type="button" class="remove-entry absolute top-2 right-2 text-red-600 hover:text-red-800">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    <div class="grid grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Language') }}</label>
            <select name="languages[{{ $index }}][id]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @foreach($languages as $lang)
                    <option value="{{ $lang->id }}" @if(isset($language) && $language->id == $lang->id) selected @endif>
                        {{ $lang->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Currency') }}</label>
            <select name="languages[{{ $index }}][currency]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @foreach(['NPR', 'USD'] as $currency)
                    <option value="{{ $currency }}" @if(isset($language) && $language->pivot->currency == $currency) selected @endif>
                        {{ $currency }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Price') }}</label>
            <input type="number" name="languages[{{ $index }}][price]" 
                value="{{ $language->pivot->price ?? '' }}"
                required min="0" step="0.01"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>
    </div>
</div>
