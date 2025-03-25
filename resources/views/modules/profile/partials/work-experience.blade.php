<div class="bg-white p-6 shadow rounded">
    <div class="flex justify-between items-center mb-4">
        <h2 class="font-semibold text-lg">{{ __('Work Experience') }}</h2>
        <button id="addExperience" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-plus mr-1"></i> {{ __('Add Experience') }}
        </button>
    </div>
    
    <div id="experienceList" class="space-y-4">
        <div class="text-center py-8 text-gray-500">{{ __('Loading work experiences...') }}</div>
    </div>
</div>

<!-- Experience Modal -->
<div id="experienceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900" id="experienceModalTitle">{{ __('Add Work Experience') }}</h3>
            <button type="button" class="text-gray-400 hover:text-gray-500" id="closeExperienceModal">
                <span class="sr-only">Close</span>
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form id="experienceForm" novalidate>
            @csrf
            <input type="hidden" name="id" id="experienceId">
            
            <div class="mb-4">
                <label for="job_title" class="block text-sm font-medium text-gray-700">{{ __('Job Title') }} <span class="text-red-500">*</span></label>
                <input type="text" name="job_title" id="job_title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <p class="mt-1 text-sm text-red-600 hidden error-message" id="job_title_error"></p>
            </div>
            
            <div class="mb-4">
                <label for="company" class="block text-sm font-medium text-gray-700">{{ __('Company') }} <span class="text-red-500">*</span></label>
                <input type="text" name="company" id="company" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <p class="mt-1 text-sm text-red-600 hidden error-message" id="company_error"></p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="start_date" class="block text-sm font-medium text-gray-700">{{ __('Start Date') }} <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" id="start_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <p class="mt-1 text-sm text-red-600 hidden error-message" id="start_date_error"></p>
                </div>
                
                <div class="mb-4">
                    <label for="end_date" class="block text-sm font-medium text-gray-700">{{ __('End Date') }}</label>
                    <input type="date" name="end_date" id="end_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">{{ __('Leave blank if current') }}</p>
                    <p class="mt-1 text-sm text-red-600 hidden error-message" id="end_date_error"></p>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">{{ __('Description') }}</label>
                <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                <p class="mt-1 text-sm text-red-600 hidden error-message" id="description_error"></p>
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" id="cancelExperience" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('Save') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Make fetchWorkExperience function available globally
    window.fetchWorkExperience = function() {
        const listContainer = document.getElementById('experienceList');
        listContainer.innerHTML = '<div class="text-center py-8 text-gray-500">{{ __("Loading work experiences...") }}</div>';
        
        axios.get('{{ route("profile.workExperience") }}')
            .then(response => {
                const experiences = response.data;
                if (experiences.length === 0) {
                    listContainer.innerHTML = '<div class="text-center py-8 text-gray-500">{{ __("No work experiences found. Add your first one!") }}</div>';
                    return;
                }
                
                listContainer.innerHTML = '';
                experiences.forEach(exp => {
                    const startDate = window.formatDate(exp.start_date);
                    const endDate = exp.end_date ? window.formatDate(exp.end_date) : '{{ __("Present") }}';
                    
                    const experienceCard = document.createElement('div');
                    experienceCard.className = 'bg-gray-50 p-4 rounded-lg shadow-sm border border-gray-200';
                    experienceCard.innerHTML = `
                        <div class="flex justify-between">
                            <h3 class="font-medium text-gray-900">${exp.job_title}</h3>
                            <div class="flex space-x-2">
                                <button onclick="editExperience(${exp.id})" class="text-indigo-600 hover:text-indigo-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </button>
                                <button onclick="deleteExperience(${exp.id})" class="text-red-600 hover:text-red-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p class="text-gray-600">${exp.company}</p>
                        <p class="text-sm text-gray-500">${startDate} - ${endDate}</p>
                        ${exp.description ? `<p class="mt-2 text-sm text-gray-600">${exp.description}</p>` : ''}
                    `;
                    
                    listContainer.appendChild(experienceCard);
                });
            })
            .catch(error => {
                console.error('Error fetching work experiences:', error);
                listContainer.innerHTML = '<div class="text-center py-4 text-red-500">{{ __("Error loading experiences. Please try again.") }}</div>';
            });
    };

    // Initialize modal behavior
    document.getElementById('addExperience').addEventListener('click', function() {
        document.getElementById('experienceForm').reset();
        document.getElementById('experienceId').value = '';
        document.getElementById('experienceModalTitle').textContent = '{{ __("Add Work Experience") }}';
        document.getElementById('experienceModal').classList.remove('hidden');
        document.querySelectorAll('.error-message').forEach(el => el.classList.add('hidden'));
    });

    document.getElementById('closeExperienceModal').addEventListener('click', function() {
        document.getElementById('experienceModal').classList.add('hidden');
    });

    document.getElementById('cancelExperience').addEventListener('click', function() {
        document.getElementById('experienceModal').classList.add('hidden');
    });

    // Handle experience form submission
    document.getElementById('experienceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Disable submit button to prevent double submission
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '{{ __("Saving...") }}';
        
        // Reset all error messages
        document.querySelectorAll('.error-message').forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
        });
        
        // Form validation - collect all errors
        let hasErrors = false;
        const errors = {};
        
        // Check required fields
        const requiredFields = ['job_title', 'company', 'start_date'];
        requiredFields.forEach(field => {
            const input = document.getElementById(field);
            if (!input.value.trim()) {
                errors[field] = '{{ __("This field is required") }}';
                hasErrors = true;
            }
        });
        
        // Check start/end date logic if both are provided
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        
        if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
            errors['end_date'] = '{{ __("End date must be after start date") }}';
            hasErrors = true;
        }
        
        // Display all validation errors at once
        if (hasErrors) {
            Object.keys(errors).forEach(field => {
                const errorElement = document.getElementById(`${field}_error`);
                if (errorElement) {
                    errorElement.textContent = errors[field];
                    errorElement.classList.remove('hidden');
                }
            });
            
            submitButton.disabled = false;
            submitButton.innerHTML = '{{ __("Save") }}';
            return;
        }
        
        // Prepare form data
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        // Submit form via axios
        axios.post('{{ route("profile.workExperience.update") }}', data)
            .then(response => {
                document.getElementById('experienceModal').classList.add('hidden');
                window.fetchWorkExperience();
                this.reset();
            })
            .catch(error => {
                console.error('Error saving experience:', error);
                if (error.response && error.response.data && error.response.data.errors) {
                    // Display all validation errors from server
                    const serverErrors = error.response.data.errors;
                    Object.keys(serverErrors).forEach(field => {
                        const errorElement = document.getElementById(`${field}_error`);
                        if (errorElement) {
                            errorElement.textContent = serverErrors[field][0];
                            errorElement.classList.remove('hidden');
                        }
                    });
                } else {
                    alert('{{ __("An error occurred while saving. Please try again.") }}');
                }
            })
            .finally(() => {
                // Re-enable submit button
                submitButton.disabled = false;
                submitButton.innerHTML = '{{ __("Save") }}';
            });
    });

    // Edit experience
    window.editExperience = function(id) {
        axios.get(`{{ url('/profile/work-experience') }}/${id}`)
            .then(response => {
                const experience = response.data;
                
                document.getElementById('experienceId').value = experience.id;
                document.getElementById('job_title').value = experience.job_title;
                document.getElementById('company').value = experience.company;
                document.getElementById('start_date').value = experience.start_date ? experience.start_date.split('T')[0] : '';
                document.getElementById('end_date').value = experience.end_date ? experience.end_date.split('T')[0] : '';
                document.getElementById('description').value = experience.description || '';
                
                document.getElementById('experienceModalTitle').textContent = '{{ __("Edit Work Experience") }}';
                document.getElementById('experienceModal').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error fetching experience for edit:', error);
                alert('{{ __("Failed to load experience details. Please try again.") }}');
            });
    };

    // Delete experience
    window.deleteExperience = function(id) {
        if (confirm('{{ __("Are you sure you want to delete this work experience?") }}')) {
            axios.delete(`{{ url('/profile/work-experience') }}/${id}`)
                .then(() => {
                    window.fetchWorkExperience();
                })
                .catch(error => {
                    console.error('Error deleting experience:', error);
                    alert('{{ __("Failed to delete experience. Please try again.") }}');
                });
        }
    };
</script>
