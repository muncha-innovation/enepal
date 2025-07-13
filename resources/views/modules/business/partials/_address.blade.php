{{-- Contact Information (moved from contact section) --}}
<div class="mb-4">
    <h3 class="text-lg font-medium text-gray-900">{{ __('Contact Information') }}</h3>
    
    <div class="mb-3">
        <label for="email" class="block text-sm font-medium leading-6 text-gray-900 required">{{ __('business.email') }}</label>
        <input required type="email" name="email" id="email" value="{{ $business->email ?? old('email') }}"
            placeholder="Eg. abc@gmail.com"
            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        <div class="validation-error" id="email-error">{{ __('Valid email is required') }}</div>
    </div>
    
    <div class="mb-3">
        <label for="phone_numbers" class="block text-sm font-medium text-gray-700 required">
            {{ __('business.phone_number') }}</label>
        <div class="mt-1" id="phone-numbers-container">
            @php
                $phoneNumbers = [];
                if (isset($business->phone_1) && $business->phone_1) {
                    $phoneNumbers = explode(',', $business->phone_1);
                } elseif (old('phone_1')) {
                    $phoneNumbers = explode(',', old('phone_1'));
                }
                if (empty($phoneNumbers)) {
                    $phoneNumbers = [''];
                }
            @endphp
            
            @foreach($phoneNumbers as $index => $phone)
                <div class="phone-number-group mb-2 flex items-center">
                    <input type="text" name="phone_numbers[]" value="{{ trim($phone) }}" 
                        minLength="6" maxLength="15" placeholder="{{ __('Eg:9812312323') }}"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        {{ $index === 0 ? 'required' : '' }}>
                    @if($index === 0)
                        <button type="button" class="ml-2 px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500" onclick="addPhoneNumber()">
                            +
                        </button>
                    @else
                        <button type="button" class="ml-2 px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500" onclick="removePhoneNumber(this)">
                            -
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
        <input type="hidden" name="phone_1" id="phone_1">
        <div class="validation-error" id="phone_1-error">{{ __('Phone number is required') }}</div>
    </div>
    
    <div class="mb-3">
        <label for="phone_2"
            class="block text-sm font-medium text-gray-700">{{ __('Contact Person Phone') }}</label>
        <input type="text" name="phone_2" id="phone_2" value="{{ $business->phone_2 ?? old('phone_2') }}"
            minLength="6" maxLength="15" placeholder={{__("Eg:9812312323")}}
            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        <div class="validation-error" id="phone_2-error"></div>
    </div>

    <div class="mb-3">
        <label for="contact_person_name" class="block text-sm font-medium text-gray-700">{{ __('Contact Person Name') }}</label>
        <input type="text" name="contact_person_name" id="contact_person_name" value="{{ $business->contact_person_name ?? old('contact_person_name') }}"
            placeholder="Eg. John Doe"
            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        <div class="validation-error" id="contact_person_name-error"></div>
    </div>
</div>

{{-- Address Information --}}
<div class="mb-4">
    <h3 class="text-lg font-medium text-gray-900">{{ __('business.business_address') }}</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Country -->
        <div>
            <label for="country" class="block text-sm font-medium text-gray-700 required">{{ __('Country') }}</label>
            <select name="address[country_id]" id="country" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                <option value="">{{ __('Select Country') }}</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}" {{ (old('address.country_id', $business->address->country_id ?? '') == $country->id) ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>
            <div data-error-for="address.country_id" class="validation-error"></div>
        </div>
        
        <!-- State -->
        <div>
            <label for="state" class="block text-sm font-medium text-gray-700">{{ __('State/Province') }}</label>
            <select name="address[state_id]" id="state" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">{{ __('Select State') }}</option>
                @if(isset($business->address->state_id))
                    <option value="{{ $business->address->state_id }}" selected>
                        {{ $business->address->state->name }}
                    </option>
                @endif
            </select>
            <div data-error-for="address.state_id" class="validation-error"></div>
        </div>
        
        <!-- City -->
        <div>
            <label for="city" class="block text-sm font-medium text-gray-700 required">{{ __('City') }}</label>
            <input type="text" name="address[city]" id="city" value="{{ old('address.city', $business->address->city ?? '') }}" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
            <div data-error-for="address.city" class="validation-error"></div>
        </div>
        
        <!-- Address Line 1 -->
        <div>
            <label for="address_line_1" class="block text-sm font-medium text-gray-700">{{ __('Address Line 1') }}</label>
            <input type="text" name="address[address_line_1]" id="address_line_1" value="{{ old('address.address_line_1', $business->address->address_line_1 ?? '') }}" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            <div data-error-for="address.address_line_1" class="validation-error"></div>
        </div>
        
        <!-- Address Line 2 -->
        <div>
            <label for="address_line_2" class="block text-sm font-medium text-gray-700">{{ __('Address Line 2') }}</label>
            <input type="text" name="address[address_line_2]" id="address_line_2" value="{{ old('address.address_line_2', $business->address->address_line_2 ?? '') }}" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            <div data-error-for="address.address_line_2" class="validation-error"></div>
        </div>
        
        <!-- Postal Code -->
        <div>
            <label for="postal_code" class="block text-sm font-medium text-gray-700">{{ __('Postal/Zip Code') }}</label>
            <input type="text" name="address[postal_code]" id="postal_code" value="{{ old('address.postal_code', $business->address->postal_code ?? '') }}" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            <div data-error-for="address.postal_code" class="validation-error"></div>
        </div>
    </div>
</div>

{{-- Map Location --}}
<div class="mb-4">
    <label for="coordinates"
        class="block text-sm font-medium leading-6 text-gray-900">{{ __('business.location') }}</label>
    <div class="mt-2 rounded-md shadow-sm">
        <input type="text" name="coordinates" id="coordinates"
            value="{{ $business->address?->location ? $business->address->location->getLat().','.$business->address->location->getLng() : '' }}"
            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
            placeholder="{{ __('business.please_select_from_map') }}" disabled>
        <input type="hidden" name="address[location]" id='location'>
    </div>
    <input id="pac-input" class="controls" type="text" placeholder="{{ __('business.search_box') }}">
    <div id="map" class="mt-3" style="height: 400px;"></div>
</div>

<script>
function addPhoneNumber() {
    const container = document.getElementById('phone-numbers-container');
    const newPhoneGroup = document.createElement('div');
    newPhoneGroup.className = 'phone-number-group mb-2 flex items-center';
    
    newPhoneGroup.innerHTML = `
        <input type="text" name="phone_numbers[]" value="" 
            minLength="6" maxLength="15" placeholder="{{ __('Eg:9812312323') }}"
            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        <button type="button" class="ml-2 px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500" onclick="removePhoneNumber(this)">
            -
        </button>
    `;
    
    container.appendChild(newPhoneGroup);
    updatePhoneNumbersInput();
}

function removePhoneNumber(button) {
    const phoneGroup = button.parentElement;
    phoneGroup.remove();
    updatePhoneNumbersInput();
}

function updatePhoneNumbersInput() {
    const phoneInputs = document.querySelectorAll('input[name="phone_numbers[]"]');
    const phoneNumbers = [];
    
    phoneInputs.forEach(input => {
        if (input.value.trim()) {
            phoneNumbers.push(input.value.trim());
        }
    });
    
    document.getElementById('phone_1').value = phoneNumbers.join(',');
}

// Update phone_1 hidden input when any phone number input changes
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('phone-numbers-container');
    
    container.addEventListener('input', function(e) {
        if (e.target.name === 'phone_numbers[]') {
            updatePhoneNumbersInput();
        }
    });
    
    // Initial update
    updatePhoneNumbersInput();
});
</script>