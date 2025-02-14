<div class="mt-6">
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Locations</label>
        <select id="location-select" name="locations[]" multiple="multiple" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @if(isset($locations) && !empty($locations))
                @foreach($locations as $location)
                    <option value="{{ $location['id'] }}" selected>{{ $location['text'] }}</option>
                @endforeach
            @endif
        </select>
    </div>
</div>

@push('js')
<script>
$(document).ready(function() {
    const select2Config = {
        placeholder: 'Search for a location...',
        minimumInputLength: 3,
        ajax: {
            transport: function (params, success, failure) {
                const autocompleteService = new google.maps.places.AutocompleteService();
                
                const request = {
                    input: params.data.term,
                    types: ['(regions)'], 
                };

                autocompleteService.getPlacePredictions(request, function(predictions, status) {
                    switch(status) {
                        case google.maps.places.PlacesServiceStatus.OK:
                            if (predictions && predictions.length > 0) {
                                const formattedResults = predictions.map(prediction => ({
                                    id: prediction.place_id,
                                    text: prediction.description
                                }));
                                success({ results: formattedResults });
                            } else {
                                success({ results: [] });
                            }
                            break;
                        case google.maps.places.PlacesServiceStatus.ZERO_RESULTS:
                            success({ results: [] });
                            break;
                        case google.maps.places.PlacesServiceStatus.OVER_QUERY_LIMIT:
                            failure('API quota exceeded');
                            break;
                        default:
                            failure('Location search failed');
                            break;
                    }
                });
            },
            processResults: function(response) {
                return response;
            },
            cache: true
        }
    };

    // Initialize Select2 with configuration
    $('#location-select').select2(select2Config);
});
</script>
@endpush
