@if(isset($typeFacilities) && $typeFacilities->count() > 0)
    <div class="mt-4">
        @foreach($typeFacilities as $facility)
            <div class="mb-2">
                @php
                    $value = $business->facilities->contains($facility->id) 
                        ? $business->facilities->find($facility->id)->pivot->value 
                        : null;
                    $type = $facility->input_type;
                @endphp
                <label for="facility_{{ $facility->id }}" class="block text-sm font-medium leading-6 text-gray-900">
                    {{ $facility->name }}
                </label>
                @if($type == 'radio')
                    <div class="flex items-center">
                        <div class="ml-2 flex items-center">
                            <input type="radio" id="facilities_{{ $facility->id }}_yes" 
                                name="facilities[{{ $facility->id }}]" value="1"
                                class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                                @if($value == '1') checked @endif />
                            <label for="facilities_{{ $facility->id }}_yes"
                                class="px-2 block text-sm leading-5 text-gray-900">{{ __('Yes') }}</label>
                        </div>
                        <div class="ml-2 flex items-center">
                            <input type="radio" id="facilities_{{ $facility->id }}_no"
                                name="facilities[{{ $facility->id }}]" value="0"
                                class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                                @if($value == '0') checked @endif />
                            <label for="facilities_{{ $facility->id }}_no"
                                class="px-2 block text-sm leading-5 text-gray-900">{{ __('No') }}</label>
                        </div>
                    </div>
                @elseif($type == 'text')
                    <div class="flex items-center">
                        <input type="text" id="facilities_{{ $facility->id }}" 
                            name="facilities[{{ $facility->id }}]"
                            value="{{ $value }}"
                            class="form-input rounded-md border-gray-300 text-indigo-600 transition duration-150 ease-in-out" />
                    </div>
                @elseif($type == 'number')
                    <div class="flex items-center">
                        <input type="number" id="facilities_{{ $facility->id }}" 
                            name="facilities[{{ $facility->id }}]"
                            value="{{ $value }}"
                            class="form-input rounded-md border-gray-300 text-indigo-600 transition duration-150 ease-in-out" />
                    </div>
                @else
                    <p>{{ __('Invalid input type') }}</p>
                @endif
            </div>
        @endforeach
    </div>
@else
    <p class="text-gray-500">{{ __('No facilities available for this business type') }}</p>
@endif