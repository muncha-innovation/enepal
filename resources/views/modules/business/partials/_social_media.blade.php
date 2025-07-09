<div id="social-media-container" class="space-y-4">
    @foreach($socialNetworks as $network)
        @php
            $businessNetwork = $business->socialNetworks->where('id', $network->id)->first();
            $url = $businessNetwork->pivot->url ?? '';
            $isActive = $businessNetwork && isset($businessNetwork->pivot->is_active) ? (bool)$businessNetwork->pivot->is_active : false;
        @endphp
        <div class="flex items-center space-x-4">
            <div class="w-1/3">
                <label for="social_network_{{ $network->id }}" class="block text-sm font-medium text-gray-700">{{ $network->name }}</label>
            </div>
            <div class="w-2/3 flex items-center space-x-4">
                <div class="flex-grow">
                    <input type="hidden" name="social_networks[{{ $network->id }}][network_id]" value="{{ $network->id }}">
                    <input type="text" name="social_networks[{{ $network->id }}][url]" id="social_network_{{ $network->id }}" value="{{ old('social_networks.'.$network->id.'.url', $url) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="{{ __('Username/URL') }}">
                    <div data-error-for="social_networks.{{ $network->id }}.url" class="validation-error"></div>
                </div>
                <div class="flex items-center">
                    <input type="hidden" name="social_networks[{{ $network->id }}][is_active]" value="0">
                    <input type="checkbox" name="social_networks[{{ $network->id }}][is_active]" id="social_network_active_{{ $network->id }}" value="1" @if(old('social_networks.'.$network->id.'.is_active', $isActive)) checked @endif class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="social_network_active_{{ $network->id }}" class="ml-2 block text-sm text-gray-900">{{ __('Active') }}</label>
                </div>
            </div>
        </div>
    @endforeach
</div>