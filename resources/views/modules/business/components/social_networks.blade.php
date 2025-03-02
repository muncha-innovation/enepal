<div class="mb-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Social Networks') }}</h3>
    <div class="space-y-4">
        @foreach($socialNetworks as $network)
            @php
                $businessNetwork = $business->socialNetworks->where('id', $network->id)->first();
            @endphp
            <div class="flex items-center space-x-4">
                <div class="w-32 flex items-center">
                    @if($network->icon)
                        <img src="{{ $network->icon }}" alt="{{ $network->name }}" class="w-6 h-6 mr-2">
                    @endif
                    <span>{{ $network->name }}</span>
                </div>
                
                <div class="flex-1">
                    <input type="text" 
                           name="social_networks[{{ $loop->index }}][url]" 
                           class="form-input rounded-md shadow-sm mt-1 block w-full"
                           value="{{ $businessNetwork->pivot->url ?? '' }}"
                           placeholder="{{ __('Username / URL') }}">
                    <input type="hidden" 
                           name="social_networks[{{ $loop->index }}][network_id]" 
                           value="{{ $network->id }}">
                </div>
                
                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" 
                               name="social_networks[{{ $loop->index }}][is_active]" 
                               class="form-checkbox" 
                               value="1"
                               {{ !isset($businessNetwork) || $businessNetwork->pivot->is_active ? 'checked' : '' }}>
                        <span class="ml-2">{{__('Active')}}</span>
                    </label>
                </div>
            </div>
        @endforeach
    </div>
</div>
