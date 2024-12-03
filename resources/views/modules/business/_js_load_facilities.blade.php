<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        document.getElementById('type_id').addEventListener('change', function() {
            const typeId = this.value;

            const facilitiesContainer = document.getElementById('facilities-container');
            facilitiesContainer.innerHTML = ''; // Clear the container before adding new fields

            // Fetch facilities based on the new business type
            fetch(`/businesses/facilities?type_id=${typeId}`)
                .then(response => response.json())
                .then(data => {
                    // if empty facilities, append some message
                    if (data.facilities.length === 0) {
                        facilitiesContainer.innerHTML =
                            '<p>{{ __('No facilities available for this business type') }}</p>';
                        return;
                    }
                    data.facilities.forEach(facility => {
                        let facilityInput = document.getElementById(
                            `facilities_${typeId}_${facility.id}`);

                        if (!facilityInput) {
                            facilityInput = document.createElement('div');
                            facilityInput.classList.add('mb-2');
                            facilityInput.id = `facilities_${typeId}_${facility.id}`;
                            facilityInput.innerHTML = getFacilityInputField(facility);
                            facilitiesContainer.appendChild(facilityInput);
                        }
                    });
                })
                .catch(error => console.error('Error fetching facilities:', error));
        });
        if (@json(!$isEdit)) {
            document.getElementById('type_id').dispatchEvent(new Event('change'));
        }
    });


    function getFacilityInputField(facility) {
        let result = '';
        if (facility.input_type == 'radio') {
            result = `
            <div class="flex items center">
                <label class="px-2 block text-sm font-bold leading-5 text-gray-900">${facility.name}</label>
                <div class=" flex items-center">
                    <input type="radio" id="facilities[${facility.id}][yes]" name="facilities[${facility.id}]" value="1" class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out" />
                    <label for="facilities[${facility.id}][yes]" class="px-2 block text-sm leading-5 text-gray-900">{{ __('Yes') }}</label>
                </div>
                <div class=" flex items-center">
                    <input type="radio" id="facilities[${facility.id}][no]" name="facilities[${facility.id}]" value="0" class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out" />
                    <label for="facilities[${facility.id}]no" class="px-2 block text-sm leading-5 text-gray-900">{{ __('No') }}</label>
                </div>
            </div>
        `;
        } else if (facility.input_type == 'text') {
            result = `
        <div class="flex items center">
            <label for="facilities[${facility.id}]" class="px-2 block text-sm leading-5 text-gray-900">${facility.name}</label>
            <input type="text" id="facilities[${facility.id}]" name="facilities[${facility.id}]" class="form-input text-indigo-600 transition duration-150 ease-in-out" />
            
        </div>
    `;
        } else if (facility.input_type == 'number') {
            result = `
        <div class="flex items center">
            <label for="facilities[${facility.id}]" class="px-2 block text-sm leading-5 text-gray-900">${facility.name}</label>
            <input type="number" id="facilities[${facility.id}]" name="facilities[${facility.id}]" class="form-input text-indigo-600 transition duration-150 ease-in-out" />
            
        </div>
        `;
        }
        return result;
    }
</script>
