<div class="bg-white p-6 shadow rounded">
    <h2 class="font-semibold text-lg mb-4">{{ __('Security Settings') }}</h2>
    <form id="securityForm" action="{{ route('profile.updatePassword') }}" method="POST" novalidate>
        @csrf
        <!-- No change needed to the form method since we're updating the route to accept POST -->
        
        <!-- Rest of the form remains the same -->
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700">{{ __('New Password') }}</label>
            <input type="password" name="password" id="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="{{ __('Enter new password') }}">
            <p class="mt-1 text-sm text-red-600 hidden" id="password-error"></p>
        </div>
        
        <div class="mb-4">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">{{ __('Confirm Password') }}</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="{{ __('Confirm new password') }}">
            <p class="mt-1 text-sm text-red-600 hidden" id="password_confirmation-error"></p>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Update Password') }}
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('securityForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Reset all error messages
        document.querySelectorAll('#securityForm .text-red-600').forEach(el => el.classList.add('hidden'));
        
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;
        
        let hasErrors = false;
        
        // Check if password is provided
        if (!password) {
            document.getElementById('password-error').textContent = '{{ __("Password is required") }}';
            document.getElementById('password-error').classList.remove('hidden');
            hasErrors = true;
        } else if (password.length < 8) {
            document.getElementById('password-error').textContent = '{{ __("Password must be at least 8 characters") }}';
            document.getElementById('password-error').classList.remove('hidden');
            hasErrors = true;
        }
        
        // Check if passwords match
        if (password !== passwordConfirmation) {
            document.getElementById('password_confirmation-error').textContent = '{{ __("Passwords do not match") }}';
            document.getElementById('password_confirmation-error').classList.remove('hidden');
            hasErrors = true;
        }
        
        if (!hasErrors) {
            this.submit();
        }
    });
</script>
