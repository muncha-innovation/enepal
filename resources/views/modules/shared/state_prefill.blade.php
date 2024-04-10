<script>
    // change state based on country
document.getElementById('country').addEventListener('change', function () {
    const countryId = this.value;
    const stateSelect = document.getElementById('state');
    stateSelect.innerHTML = '';
    axios.get(`/api/countries/${countryId}/states`)
        .then(response => {
            const states = response.data;
            states.forEach(state => {
                const option = document.createElement('option');
                option.value = state.id;
                option.textContent = state.name;
                if (state.id == @json($entity->address?->state_id)) {
                    option.selected = true;
                }
                stateSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error(error);
        });
});
// prefill phone based on country code
document.getElementById('country').addEventListener('change', function () {
    const countryId = this.value;
    const country = @json($countries);
    const phoneInput = document.getElementById('phone');
    const countryData = country.find(c => c.id == countryId);
    phoneInput.value = `${countryData.dial_code}`;
});
document.getElementById('country').dispatchEvent(new Event('change'));

</script>