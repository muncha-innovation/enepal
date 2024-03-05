 <div>
   <input type="hidden" name="postal_code" id="postal_code" value="">
   <fieldset class="col-span-2 mb-2">
     <label for="postalCode" class="block text-sm font-medium text-gray-700">
       {{ __('Postal Code') }}
     </label>
     <div class="flex justify-between gap-2" id="postalCode">
       <div class="mt-1 w-full">
         <input type="text" name="postalCode1" id="postal_code1" onkeyup="checkAddress()"
           class="postal-code block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
           placeholder="" value="{{ $isEdit ? $postal_code1 ?? '' : old('postalCode1') }}" />
       </div>

       <div class="mt-1 w-full">
         <input type="text" name="postalCode2" id="postal_code2" onkeyup="checkAddress()"
           class="postal-code block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
           placeholder="" value="{{ $isEdit ? $postal_code2 ?? '' : old('postalCode2') }}" />
       </div>
       @error('postal_code1')
         <span class="text-red-500">{{ $message }}</span>
       @enderror
       @error('postal_code2')
         <span class="text-red-500">{{ $message }}</span>
       @enderror
     </div>
   </fieldset>

   <fieldset class="col-span-2 mb-2">
     <label for="prefecture" class="block text-sm font-medium text-gray-700">
       {{ __('Prefecture') }}
     </label>
     <div class="mt-1">
       <input type="text" name="address[prefecture]" id="prefecture"
         class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
         placeholder="" value="{{ $isEdit ? $user?->address?->prefecture : old('address.prefecture') }}" />
     </div>
   </fieldset>
   <fieldset class="col-span-2 mb-2">
     <label for="city" class="block text-sm font-medium text-gray-700">
       {{ __('City') }}
     </label>
     <div class="mt-1">
       <input type="text" name="address[city]" id="city"
         class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
         placeholder="" value="{{ $isEdit ? $user?->address?->city : old('city') }}" />
     </div>
   </fieldset>
   <fieldset class="col-span-2 mb-2">
     <label for="address[town]" class="block text-sm font-medium text-gray-700">
       {{ __('Town') }}
     </label>
     <div class="mt-1">
       <input type="text" name="address[town]" id="town"
         class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
         placeholder="" value="{{ $isEdit ? $user?->address?->town : old('address.town') }}" />
     </div>
   </fieldset>
   <fieldset class="col-span-2 mb-2">
     <label for="streetAddress" class="block text-sm font-medium text-gray-700">
       {{ __('Street Address') }}
     </label>
     <div class="mt-1">
       <input type="text" name="address[street]" id="streetAddress"
         class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
         placeholder="" value="{{ $isEdit ? $user?->address?->street : old('address.street') }}" />
     </div>
   </fieldset>

 </div>
