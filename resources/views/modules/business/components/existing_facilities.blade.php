@foreach ($business->facilities as $facility)
    @php
        $value = $facility->pivot->value;
        $type = $facility->input_type;
    @endphp

    <div class="mb-2">
        <label for="facility_{{ $facility->id }}" class="block text-sm font-medium leading-6 text-gray-900">
            {{ $facility->title }}
        </label>
        @if ($type == 'radio')
            <div class="flex items-center">

                <label class="block text-sm font-bold leading-5 text-gray-900">{{ $facility->name }}</label>
                <div class="ml-2 flex items-center">
                    <input type="radio" id="facilities_{{ $business->type->id }}_{{ $facility->id }}"
                        name="facilities[{{ $facility->id }}]" value="1"
                        class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                        @if ($value == '1') checked @endif />
                    <label for="facilities[{{ $facility->id }}][yes]"
                        class="ml-2 block text-sm leading-5 text-gray-900">{{ __('Yes') }}</label>
                </div>
                <div class="ml-2 flex items-center">
                    <input type="radio" id="facilities[{{ $facility->id }}][no]"
                        name="facilities[{{ $facility->id }}]" value="0"
                        class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                        @if ($value == '0') checked @endif />
                    <label for="facilities[{{ $facility->id }}][no]"
                        class="ml-2 block text-sm leading-5 text-gray-900">{{ __('No') }}</label>
                </div>
            </div>
        @elseif ($type == 'checkbox')
            <div class="flex items-center">
                <input type="checkbox" id="facility_{{ $facility->id }}" name="facilities[{{ $facility->id }}]"
                    value="1" class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                    @if ($value == '1') checked @endif />
                <label for="facility_{{ $facility->id }}"
                    class="ml-2 block text-sm leading-5 text-gray-900">{{ $facility->title }}</label>
            </div>
        @elseif ($type == 'text')
            <div class="flex items-center">
                <input type="text" id="facility_{{ $facility->id }}" name="facilities[{{ $facility->id }}]"
                    value="{{ $value }}"
                    class="form-input h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                    placeholder="{{ $facility->facility->description ?? '' }}">
                <label for="facility_{{ $facility->id }}"
                    class="ml-2 block text-sm leading-5 text-gray-900">{{ $facility->title }}</label>
            </div>
        @elseif ($type == 'number')
            <div class="flex items-center">
                <input type="number" id="facility_{{ $facility->id }}" name="facilities[{{ $facility->id }}]"
                    value="{{ $value }}"
                    class="form-input h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                    placeholder="{{ $facility->facility->description ?? '' }}">
                <label for="facility_{{ $facility->id }}"
                    class="ml-2 block text-sm leading-5 text-gray-900">{{ $facility->title }}</label>
            </div>
        @else
            <p>Invalid input type</p>
        @endif
    </div>
@endforeach
