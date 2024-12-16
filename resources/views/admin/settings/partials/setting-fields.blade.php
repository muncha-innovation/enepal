@foreach ($typeSettings->groupBy('key') as $key => $keySettings)
    <div class="bg-gray-50 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $formatLabel($key) }}</h3>

        @foreach ($keySettings as $setting)
            @php
                $value = is_string($setting->value) ? json_decode($setting->value, true) : $setting->value;
            @endphp

            @if (is_array($value))
                @foreach ($value as $fieldKey => $fieldValue) 
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ $formatLabel($fieldKey) }}
                        </label>

                        @if (in_array($fieldKey, $booleanFields))
                            <select name="settings[{{ $type }}][{{ $key }}][{{ $fieldKey }}]"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="1" {{ $fieldValue == 1 ? 'selected' : '' }}>Enabled</option>
                                <option value="0" {{ $fieldValue == 0 ? 'selected' : '' }}>Disabled</option>
                            </select>
                        @elseif (str_contains($fieldKey, 'description'))
                            <textarea name="settings[{{ $type }}][{{ $key }}][{{ $fieldKey }}]"
                                class="rich-text-editor mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >{{ $fieldValue }}</textarea>
                        @else
                            <input type="text"
                                name="settings[{{ $type }}][{{ $key }}][{{ $fieldKey }}]"
                                value="{{ is_array($fieldValue) ? json_encode($fieldValue) : $fieldValue }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @endif
                    </div>
                @endforeach
            @else
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $formatLabel($key) }}
                    </label>
                    <input type="text"
                        name="settings[{{ $type }}][{{ $key }}]"
                        value="{{ is_array($value) ? json_encode($value) : $value }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            @endif
        @endforeach
    </div>
@endforeach 