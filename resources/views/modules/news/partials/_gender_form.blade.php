<div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-4">
            
                    <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('Genders') }}</h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach ($genders as $gender)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="genders[]" value="{{ $gender->id }}"
                                    @if($news->exists) 
                                        {{ $news->genders->contains($gender->id) ? 'checked' : '' }}
                                    @else
                                        checked
                                    @endif
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">{{ $gender->name }}</span>
                            </label>
                        @endforeach
                    </div>
    </div>
</div> 