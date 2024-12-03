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

                <label class="px-2 block text-sm font-bold leading-5 text-gray-900">{{ $facility->name }}</label>
                <div class="ml-2 flex items-center">
                    <input type="radio" id="facilities_{{ $business->type->id }}_{{ $facility->id }}"
                        name="facilities[{{ $facility->id }}]" value="1"
                        class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                        @if ($value == '1') checked @endif />
                    <label for="facilities[{{ $facility->id }}][yes]"
                        class="px-2 block text-sm leading-5 text-gray-900">{{ __('Yes') }}</label>
                </div>
                <div class="ml-2 flex items-center">
                    <input type="radio" id="facilities[{{ $facility->id }}][no]"
                        name="facilities[{{ $facility->id }}]" value="0"
                        class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                        @if ($value == '0') checked @endif />
                    <label for="facilities[{{ $facility->id }}][no]"
                        class="px-2 block text-sm leading-5 text-gray-900">{{ __('No') }}</label>
                </div>
            </div>
        @elseif ($type == 'text')
            <div class="flex items-center">

                <label for="facility_{{ $facility->id }}"
                    class="px-2 block text-sm font-bold leading-5 text-gray-900">{{ $facility->name }}</label>
                <input type="text" id="facility_{{ $facility->id }}" name="facilities[{{ $facility->id }}]"
                    value="{{ $value }}"
                    class="form-input text-indigo-600 transition duration-150 ease-in-out"
                    placeholder="{{ $facility->facility->description ?? '' }}">
            </div>
        @elseif ($type == 'number')
            <div class="flex items-center">
                <label for="facility_{{ $facility->id }}"
                    class="px-2 block text-sm font-bold leading-5 text-gray-900">{{ $facility->name }}</label>
                <input type="number" id="facility_{{ $facility->id }}" name="facilities[{{ $facility->id }}]"
                    value="{{ $value }}"
                    class="form-input text-indigo-600 transition duration-150 ease-in-out"
                    placeholder="{{ $facility->facility->description ?? '' }}">

            </div>
        @else
            <p>{{__('Invalid input type')}}</p>
        @endif
    </div>
@endforeach
