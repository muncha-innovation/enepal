<div class="mb-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Business Hours') }}</h3>

    <div class="mb-4">
        <button type="button"
                onclick="copyMondayToAll()"
                class="text-sm text-blue-600 hover:underline">
            {{ __('Copy Monday hours to all days') }}
        </button>
    </div>

    @if($errors->has('hours'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ $errors->first('hours') }}</span>
        </div>
    @endif
    <div data-error-for="hours" class="validation-error"></div>

    @php
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $hours = $business->hours ?? collect();
    @endphp

    @foreach($days as $day)
        @php
            $dayHours = $hours->where('day', $day)->first();
            $isOpen = $dayHours ? $dayHours->is_open : false;
        @endphp

        <div class="bg-gray-50 p-4 rounded-md mb-2">
            <div class="flex items-center justify-between">
                <label class="font-medium">
                    <input type="checkbox"
                           name="hours[{{ $day }}][is_open]"
                           class="form-checkbox mr-2"
                           value="1"
                           {{ $isOpen ? 'checked' : '' }}
                           onchange="toggleTimeRow(this)">
                    {{ $day }}
                </label>

                <div class="flex items-center gap-2 time-row {{ !$isOpen ? 'hidden' : '' }}">
                    <input type="time"
                           class="form-input rounded-md"
                           data-day="{{ $day }}"
                           data-type="open"
                           name="hours[{{ $day }}][open_time]"
                           value="{{ $dayHours && $isOpen ? \Carbon\Carbon::parse($dayHours->open_time)->format('H:i') : '' }}">
                    <span>–</span>
                    <input type="time"
                           class="form-input rounded-md"
                           data-day="{{ $day }}"
                           data-type="close"
                           name="hours[{{ $day }}][close_time]"
                           value="{{ $dayHours && $isOpen ? \Carbon\Carbon::parse($dayHours->close_time)->format('H:i') : '' }}">
                </div>
            </div>
        </div>
    @endforeach
</div>

<script>
function toggleTimeRow(checkbox) {
    const parent = checkbox.closest('.flex');
    const timeRow = parent.querySelector('.time-row');
    const inputs = timeRow.querySelectorAll('input[type="time"]');

    timeRow.classList.toggle('hidden', !checkbox.checked);

    inputs.forEach(input => {
        input.disabled = !checkbox.checked;
        if (!checkbox.checked) {
            input.value = '';
        }
    });
}

function copyMondayToAll() {
    const open = document.querySelector('[data-day="Monday"][data-type="open"]')?.value;
    const close = document.querySelector('[data-day="Monday"][data-type="close"]')?.value;
    const isOpen = document.querySelector('input[name="hours[Monday][is_open]"]')?.checked;

    if (!isOpen || !open || !close) {
        alert('Please set Monday\'s hours first.');
        return;
    }

    const days = ['Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    days.forEach(day => {
        document.querySelector(`input[name="hours[${day}][is_open]"]`).checked = true;
        const openInput = document.querySelector(`[data-day="${day}"][data-type="open"]`);
        const closeInput = document.querySelector(`[data-day="${day}"][data-type="close"]`);
        const timeRow = openInput.closest('.time-row');
        
        openInput.disabled = false;
        closeInput.disabled = false;
        openInput.value = open;
        closeInput.value = close;
        timeRow.classList.remove('hidden');
    });
}
</script>
