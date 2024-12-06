<div x-data="{ country: '{{ $isEdit ? $user?->address?->country : old('address.country', '') }}' }">
  <fieldset class="col-span-2 mb-2">
    <label for="address[country]" class="block text-sm font-medium text-gray-700">{{ __('Country') }}</label>
    <select id="address[country]" name="address[country]" x-model="country"
      class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm" required>
      <option value="">{{ __('Select Option') }}</option>
      @foreach ($countries as $country)
        <option value="{{ $country}}"
          @if ($isEdit && $country == $userCountry) selected @endif>{{ $country }}</option>
      @endforeach
    </select>
  </fieldset>
    @include('admin.users.partials.general_form')
  
</div>
