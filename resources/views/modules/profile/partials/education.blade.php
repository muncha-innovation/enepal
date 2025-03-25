<div class="bg-white p-6 shadow rounded">
    <div class="flex justify-between items-center mb-4">
        <h2 class="font-semibold text-lg">{{ __('Education') }}</h2>
        <button id="addEducation" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-plus mr-1"></i> {{ __('Add Education') }}
        </button>
    </div>
    
    <div id="educationList" class="space-y-4">
        <div class="text-center py-8 text-gray-500">{{ __('Loading education entries...') }}</div>
    </div>
</div>

<!-- Education Modal -->
<div id="educationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900" id="educationModalTitle">{{ __('Add Education') }}</h3>
            <button type="button" class="text-gray-400 hover:text-gray-500" id="closeEducationModal">
                <span class="sr-only">Close</span>
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form id="educationForm" novalidate>
            @csrf
            <input type="hidden" name="id" id="educationId">
            
            <div class="mb-4">
                <label for="type" class="block text-sm font-medium text-gray-700">{{ __('Education Level') }} <span class="text-red-500">*</span></label>
                <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">{{ __('Select Education Level') }}</option>
                    <option value="under_slc">{{ __('Below SLC') }}</option>
                    <option value="slc">{{ __('SLC/SEE') }}</option>
                    <option value="plus_two">{{ __('Plus Two/Intermediate') }}</option>
                    <option value="bachelors">{{ __('Bachelor\'s Degree') }}</option>
                    <option value="masters">{{ __('Master\'s Degree') }}</option>
                    <option value="phd">{{ __('PhD/Doctorate') }}</option>
                    <option value="training">{{ __('Training/Certificate') }}</option>
                </select>
                <p class="mt-1 text-sm text-red-600 hidden error-message" id="type_error"></p>
            </div>
            
            <div class="mb-4">
                <label for="institution" class="block text-sm font-medium text-gray-700">{{ __('Institution') }} <span class="text-red-500">*</span></label>
                <input type="text" name="institution" id="institution" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <p class="mt-1 text-sm text-red-600 hidden error-message" id="institution_error"></p>
            </div>
            
            <div class="mb-4">
                <label for="degree" class="block text-sm font-medium text-gray-700">{{ __('Degree/Certificate') }}</label>
                <input type="text" name="degree" id="degree" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <p class="mt-1 text-sm text-red-600 hidden error-message" id="degree_error"></p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="edu_start_date" class="block text-sm font-medium text-gray-700">{{ __('Start Date') }} <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" id="edu_start_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <p class="mt-1 text-sm text-red-600 hidden error-message" id="edu_start_date_error"></p>
                </div>
                
                <div class="mb-4">
                    <label for="edu_end_date" class="block text-sm font-medium text-gray-700">{{ __('End Date') }}</label>
                    <input type="date" name="end_date" id="edu_end_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">{{ __('Leave blank if current') }}</p>
                    <p class="mt-1 text-sm text-red-600 hidden error-message" id="end_date_error"></p>
                </div>
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" id="cancelEducation" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
    // Education type labels map
    const educationTypeLabels = {
        'under_slc': '{{ __("Below SLC") }}',
        'slc': '{{ __("SLC/SEE") }}',
        'plus_two': '{{ __("Plus Two/Intermediate") }}',
        'bachelors': '{{ __("Bachelor\'s Degree") }}',
        'masters': '{{ __("Master\'s Degree") }}',
        'phd': '{{ __("PhD/Doctorate") }}',
        'training': '{{ __("Training/Certificate") }}'
    };

    // Date formatting function
    function formatDate(dateString) {
        if (!dateString) return '{{ __("Present") }}';
        
        const date = new Date(dateString);
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    }

    // Make fetchEducation function available globally
    window.fetchEducation = function() {
        const listContainer = document.getElementById('educationList');
        listContainer.innerHTML = '<div class="text-center py-8 text-gray-500">{{ __("Loading education entries...") }}</div>';
        
        axios.get('{{ route("profile.education") }}')
            .then(response => {
                const educationEntries = response.data;
                if (educationEntries.length === 0) {
                    listContainer.innerHTML = '<div class="text-center py-8 text-gray-500">{{ __("No education entries found. Add your first one!") }}</div>';
                    return;
                }
                
                listContainer.innerHTML = '';
                educationEntries.forEach(edu => {
                    const endDate = edu.end_date ? formatDate(edu.end_date) : '{{ __("Present") }}';
                    const startDate = formatDate(edu.start_date);
                    const educationType = educationTypeLabels[edu.type] || edu.type;
                    
                    // Create a badge for training type
                    const isTraining = edu.type === 'training';
                    const trainingBadge = isTraining ? 
                        `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                            </svg>
                            {{ __("Training") }}
                        </span>` : '';
                    
                    const educationCard = document.createElement('div');
                    educationCard.className = 'bg-gray-50 p-4 rounded-lg shadow-sm border border-gray-200';
                    educationCard.innerHTML = `
                        <div class="flex justify-between">
                            <h3 class="font-medium text-gray-900 flex items-center">
                                ${edu.institution}${trainingBadge}
                            </h3>
                            <div class="flex space-x-2">
                                <button onclick="editEducation(${edu.id})" class="text-indigo-600 hover:text-indigo-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </button>
                                <button onclick="deleteEducation(${edu.id})" class="text-red-600 hover:text-red-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p class="text-gray-600">${edu.degree || ''}</p>
                        <p class="text-sm text-gray-500">${educationType}</p>
                        <p class="text-sm text-gray-500">${startDate} - ${endDate}</p>
                    `;
                    
                    listContainer.appendChild(educationCard);
                });
            })
            .catch(error => {
                console.error('Error fetching education:', error);
                listContainer.innerHTML = '<div class="text-center py-4 text-red-500">{{ __("Error loading education entries. Please try again.") }}</div>';
            });
    }

    // Open education modal for adding
    document.getElementById('addEducation').addEventListener('click', function() {
        document.getElementById('educationForm').reset();
        document.getElementById('educationId').value = '';
        document.getElementById('educationModalTitle').textContent = '{{ __("Add Education") }}';
        document.getElementById('educationModal').classList.remove('hidden');
        document.querySelectorAll('.error-message').forEach(el => el.classList.add('hidden'));
    });

    // Close education modal
    document.getElementById('closeEducationModal').addEventListener('click', function() {
        document.getElementById('educationModal').classList.add('hidden');
    });

    document.getElementById('cancelEducation').addEventListener('click', function() {
        document.getElementById('educationModal').classList.add('hidden');
    });

    // Handle education form submission - improved validation to show all errors at once
    document.getElementById('educationForm').addEventListener('submit', function(e) {
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
        if (!document.getElementById('type').value.trim()) {
            errors['type'] = '{{ __("Please select an education level") }}';
            hasErrors = true;
        }
        
        if (!document.getElementById('institution').value.trim()) {
            errors['institution'] = '{{ __("Institution name is required") }}';
            hasErrors = true;
        }
        
        // Fix the start date validation - use the correct error element ID
        if (!document.getElementById('edu_start_date').value.trim()) {
            errors['edu_start_date'] = '{{ __("Start date is required") }}';
            hasErrors = true;
        }
        
        // Check start/end date logic if both are provided
        const startDate = document.getElementById('edu_start_date').value;
        const endDate = document.getElementById('edu_end_date').value;
        
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
        
        // Fix form data - ensure correct field names
        const data = {
            id: formData.get('id'),
            type: formData.get('type'),
            institution: formData.get('institution'),
            degree: formData.get('degree'),
            start_date: formData.get('start_date'),
            end_date: formData.get('end_date'),
        };
        
        // Submit form via axios
        axios.post('{{ route("profile.education.update") }}', data)
            .then(response => {
                document.getElementById('educationModal').classList.add('hidden');
                window.fetchEducation(); // Use the global function
                this.reset();
            })
            .catch(error => {
                console.error('Error saving education:', error);
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

    // Edit education
    window.editEducation = function(id) {
        axios.get(`{{ url('/profile/education') }}/${id}`)
            .then(response => {
                const education = response.data;
                
                document.getElementById('educationId').value = education.id;
                document.getElementById('type').value = education.type;
                document.getElementById('institution').value = education.institution;
                document.getElementById('degree').value = education.degree || '';
                document.getElementById('edu_start_date').value = education.start_date ? education.start_date.split('T')[0] : '';
                document.getElementById('edu_end_date').value = education.end_date ? education.end_date.split('T')[0] : '';
                
                document.getElementById('educationModalTitle').textContent = '{{ __("Edit Education") }}';
                document.getElementById('educationModal').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error fetching education for edit:', error);
                alert('{{ __("Failed to load education details. Please try again.") }}');
            });
    };

    // Delete education
    window.deleteEducation = function(id) {
        if (confirm('{{ __("Are you sure you want to delete this education entry?") }}')) {
            axios.delete(`{{ url('/profile/education') }}/${id}`)
                .then(() => {
                    window.fetchEducation(); // Use the global function
                })
                .catch(error => {
                    console.error('Error deleting education:', error);
                    alert('{{ __("Failed to delete education entry. Please try again.") }}');
                });
        }
    };
</script>
