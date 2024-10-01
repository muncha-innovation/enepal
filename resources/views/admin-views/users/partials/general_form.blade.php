{{-- <fieldset class="col-span-2 mb-2">
  <label for="address1" class="block text-sm font-medium text-gray-700">
    {{ __('Address 1') }}
  </label>
  <div class="mt-1">
    <input type="text" name="address1" id="address1"
      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
      placeholder="" />
  </div>
</fieldset>
<fieldset class="col-span-2 mb-2">
  <label for="address2" class="block text-sm font-medium text-gray-700">
    {{ __('Address 2') }}
  </label>
  <div class="mt-1">
    <input type="text" name="address2" id="address2"
      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
      placeholder="" />
  </div>
</fieldset> --}}

<div>
  <fieldset class="col-span-2 mb-2">
    <label for="city" class="block text-sm font-medium text-gray-700">
      {{ __('City') }}
    </label>
    <div class="mt-1">
      <input type="text" name="address[city]" id="city"
        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        placeholder="" value="{{ $isEdit ? $user->primaryAddress?->city : old('address.city') }}" />
    </div>
  </fieldset>

  <fieldset class="col-span-2 mb-2">
    <label for="state" class="block text-sm font-medium text-gray-700">
      {{ __('State') }}
    </label>
    <div class="mt-1">
      <input type="text" name="address[state]" id="state"
        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        placeholder="" value="{{ $isEdit ? $user->primaryAddress?->state : old('address.state') }}" />
    </div>
  </fieldset>
  <fieldset class="col-span-2 mb-2">
    <label for="streetAddress" class="block text-sm font-medium text-gray-700">
      {{ __('Street Address') }}
    </label>
    <div class="mt-1">
      <input type="text" name="address[street]" id="streetAddress"
        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        placeholder="" value="{{ $isEdit ? $user->primaryAddress?->street : old('address.street') }}" />
    </div>
  </fieldset>
  <fieldset class="col-span-2 mb-2">
    <label for="zip" class="block text-sm font-medium text-gray-700">
      {{ __('Zip/Postal Code') }}
    </label>
    <div class="mt-1">
      <input type="text" name="address[postal_code]" id="zip"
        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        placeholder="" value="{{ $isEdit ? $user->primaryAddress?->postal_code : old('address.postal_code') }}" />
    </div>
  </fieldset>
</div>
