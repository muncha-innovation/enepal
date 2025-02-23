<div class="mb-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Business Hours') }}</h3>
    <div class="space-y-4">
        @php
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            $hours = $business->hours ?? collect();
        @endphp

        @foreach($days as $day)
            @php
                $dayHours = $hours->where('day', $day)->first();
                // Only set isOpen to true if we have a record and it's explicitly marked as open
                $isOpen = $dayHours ? $dayHours->is_open : false;
            @endphp
            <div class="flex items-center space-x-4">
                <div class="w-32">
                    <label class="inline-flex items-center">
                        <input type="checkbox" 
                               name="hours[{{ $day }}][is_open]" 
                               class="form-checkbox" 
                               value="1"
                               {{ $isOpen ? 'checked' : '' }}
                               onchange="toggleTimeInputs('{{ $day }}', this.checked)">
                        <span class="ml-2">{{ $day }}</span>
                    </label>
                </div>
                
                <div id="{{ $day }}_times" class="flex items-center space-x-2 {{ !$isOpen ? 'hidden' : '' }}">
                    <input type="time" 
                           name="hours[{{ $day }}][open_time]" 
                           class="form-input rounded-md shadow-sm mt-1 block"
                           value="{{ $dayHours && $isOpen ? \Carbon\Carbon::parse($dayHours->open_time)->format('H:i') : '' }}"
                           {{ !$isOpen ? 'disabled' : '' }}
                           data-field="open_time">
                    
                    <span>to</span>
                    
                    <input type="time" 
                           name="hours[{{ $day }}][close_time]" 
                           class="form-input rounded-md shadow-sm mt-1 block"
                           value="{{ $dayHours && $isOpen ? \Carbon\Carbon::parse($dayHours->close_time)->format('H:i') : '' }}"
                           {{ !$isOpen ? 'disabled' : '' }}
                           data-field="close_time">
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
function toggleTimeInputs(day, isChecked) {
    const timesDiv = document.getElementById(day + '_times');
    const inputs = timesDiv.querySelectorAll('input[type="time"]');
    
    timesDiv.classList.toggle('hidden', !isChecked);
    
    inputs.forEach(input => {
        if (!isChecked) {
            input.value = ''; // Clear the time when unchecked
            input.name = ''; // Remove from form submission
            input.disabled = true;
        } else {
            input.name = `hours[${day}][${input.getAttribute('data-field')}]`;
            input.disabled = false;
        }
    });
}
</script>
