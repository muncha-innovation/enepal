<div class="space-y-4 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700">Title</label>
        <input type="text" name="title" value="{{ old('title', $news->title) }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('title')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $news->description) }}</textarea>
        @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Source URL (Optional)</label>
        <input type="url" name="url" value="{{ old('url', $news->url) }}" placeholder="https://example.com/news-article"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('url')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label for="image" class="block text-sm font-medium text-gray-700">{{ __('Featured Image') }}</label>
        <div class="mt-1">
            <input type="file" id="image-file" accept="image/*" onchange="uploadImage(this)"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            
            <div class="mt-2 text-xs text-gray-500 bg-blue-50 p-2 rounded-md">
                <p class="font-medium text-blue-700">📐 Preferred aspect ratio: 16:9 (Widescreen)</p>
                <p>Recommended size: 1200x675 pixels for optimal web display</p>
            </div>
            
            <input type="hidden" id="image-url-input" name="image" value="{{ old('image', $news->image ?? '') }}">
            
            <div id="image-preview-container" class="mt-2">
                @if(isset($news) && $news->image)
                    <img src="{{ $news->image }}" id="image-preview" alt="Preview" 
                        class="rounded-lg border border-gray-200 object-cover" 
                        style="width: 200px; aspect-ratio: 16 / 9;">
                @endif
            </div>
        </div>
    </div>

    <div class="flex items-center space-x-4">
        <div class="flex items-center">
            <input type="checkbox" name="is_active" id="is_active" value="1" 
                @if(old('is_active') !== null)
                    {{ old('is_active') ? 'checked' : '' }}
                @elseif($news->exists) 
                    {{ $news->is_active ? 'checked' : '' }}
                @else
                    checked
                @endif
                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
        </div>
        
        <div class="flex items-center">
            <input type="checkbox" name="is_featured" id="is_featured" value="1" 
                @if(old('is_featured') !== null)
                    {{ old('is_featured') ? 'checked' : '' }}
                @elseif($news->exists) 
                    {{ $news->is_featured ? 'checked' : '' }}
                @else
                    checked
                @endif
                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            <label for="is_featured" class="ml-2 text-sm text-gray-700">Featured</label>
        </div>
    </div>

    <div class="mb-6">
        @if($news->url)
            <a href="{{ $news->url }}" 
               target="_blank"
               class="inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Visit Original News
            </a>
        @endif
    </div>
    @include('modules.news.partials._categories_form')
    @include('modules.news.partials._gender_form')
    @include('modules.news.partials._location_form', ['locations' => $locations ?? null])

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Age Groups</label>
        <div class="mt-2 space-y-2">
            @foreach($ageGroups as $ageGroup)
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="age_groups[]" 
                           value="{{ $ageGroup->id }}"
                           id="age-group-{{ $ageGroup->id }}"
                           @if(old('age_groups'))
                               {{ in_array($ageGroup->id, old('age_groups', [])) ? 'checked' : '' }}
                           @elseif($news->exists) 
                               {{ $news->ageGroups->contains($ageGroup->id) ? 'checked' : '' }}
                           @else
                               checked
                           @endif
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <label for="age-group-{{ $ageGroup->id }}" class="ml-2 text-sm text-gray-700">
                        {{ $ageGroup->name }}
                    </label>
                </div>
            @endforeach
        </div>
        @error('age_groups')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Tags</label>
        <div class="mt-1">
            <select id="tags" name="tags[]" multiple class="w-full">
                @php
                    $selectedTags = old('tags', $news->tags ? $news->tags->pluck('name')->toArray() : []);
                @endphp
                @foreach($selectedTags as $tag)
                    <option value="{{ $tag }}" selected>{{ $tag }}</option>
                @endforeach
            </select>
        </div>
        @error('tags')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex justify-end">
        <button type="submit" id="submit-btn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
            {{ $news->exists ? 'Update' : 'Create' }} News
        </button>
    </div>
</div> 

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://maps.googleapis.com/maps/api/js?key={{config('services.google.places.api_key')}}&libraries=places&v=weekly"></script>
<script>
// Client-side validation for news form
class NewsFormValidator {
    constructor() {
        this.form = null;
        this.errors = {};
        this.init();
    }

    init() {
        // Wait for DOM to be ready
        $(document).ready(() => {
            this.form = $('form').has('input[name="title"]').first();
            this.setupRealTimeValidation();
            this.setupFormSubmission();
            
            // Initial validation to set submit button state
            setTimeout(() => {
                this.validateAll();
                this.updateSubmitButton();
            }, 500); // Small delay to ensure Select2 is initialized
        });
    }

    setupRealTimeValidation() {
        // Validate on blur for text inputs
        this.form.find('input[name="title"]').on('blur', () => {
            this.validateTitle();
            this.updateSubmitButton();
        });
        this.form.find('textarea[name="description"]').on('blur', () => {
            this.validateDescription();
            this.updateSubmitButton();
        });
        this.form.find('input[name="url"]').on('blur', () => {
            this.validateUrl();
            this.updateSubmitButton();
        });
        
        // Validate on input for immediate feedback
        this.form.find('input[name="title"]').on('input', () => {
            this.validateTitle();
            this.updateSubmitButton();
        });
        this.form.find('textarea[name="description"]').on('input', () => {
            this.validateDescription();
            this.updateSubmitButton();
        });
        this.form.find('input[name="url"]').on('input', () => {
            this.validateUrl();
            this.updateSubmitButton();
        });
        
        // Validate checkboxes when changed
        this.form.find('input[type="checkbox"]').on('change', () => {
            this.validateCheckboxArrays();
            this.updateSubmitButton();
        });
        
        // Validate Select2 when changed
        $('#tags').on('change', () => {
            this.validateTags();
            this.updateSubmitButton();
        });
        $('#location-select').on('change', () => {
            this.validateLocations();
            this.updateSubmitButton();
        });
    }

    setupFormSubmission() {
        this.form.on('submit', (e) => {
            this.validateAll();
            
            if (Object.keys(this.errors).length > 0) {
                e.preventDefault();
                this.showGlobalErrorMessage();
                this.scrollToFirstError();
                return false;
            } else {
                // Clear any existing global errors on successful submission
                $('.global-validation-error').remove();
            }
        });
    }

    validateTitle() {
        const title = this.form.find('input[name="title"]').val().trim();
        
        if (!title) {
            this.setError('title', 'Title is required');
        } else if (title.length > 255) {
            this.setError('title', 'Title cannot be longer than 255 characters');
        } else {
            this.clearError('title');
        }
    }

    validateDescription() {
        const description = this.form.find('textarea[name="description"]').val().trim();
        
        if (!description) {
            this.setError('description', 'Description is required');
        } else {
            this.clearError('description');
        }
    }

    validateUrl() {
        const url = this.form.find('input[name="url"]').val().trim();
        
        if (url && !this.isValidUrl(url)) {
            this.setError('url', 'Please enter a valid URL');
        } else {
            this.clearError('url');
        }
    }

    validateTags() {
        const tags = $('#tags').val() || [];
        let hasError = false;
        
        tags.forEach(tag => {
            if (tag.length > 50) {
                this.setError('tags', 'Each tag cannot be longer than 50 characters');
                hasError = true;
            }
        });
        
        if (!hasError) {
            this.clearError('tags');
        }
    }

    validateLocations() {
        // Locations are optional, but if provided should be valid
        this.clearError('locations');
    }

    validateCheckboxArrays() {
        // Categories, genders, and age_groups are optional arrays
        // No specific validation needed for these checkbox arrays
        // as they're all optional according to the backend rules
    }

    validateAll() {
        this.errors = {};
        this.validateTitle();
        this.validateDescription();
        this.validateUrl();
        this.validateTags();
        this.validateLocations();
        this.validateCheckboxArrays();
    }

    isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

    setError(field, message) {
        this.errors[field] = message;
        this.displayError(field, message);
    }

    clearError(field) {
        delete this.errors[field];
        this.hideError(field);
    }

    displayError(field, message) {
        const fieldElement = this.getFieldElement(field);
        if (!fieldElement) return;

        // Remove existing error
        if (field === 'tags' || field === 'locations') {
            // For Select2 fields, find the container and add error after it
            fieldElement.closest('.select2-container').siblings('.validation-error').remove();
        } else {
            fieldElement.siblings('.validation-error').remove();
        }
        
        // Add error styling
        if (field === 'tags' || field === 'locations') {
            // Style Select2 container
            fieldElement.addClass('border-red-500');
            fieldElement.removeClass('border-gray-300');
        } else {
            fieldElement.addClass('border-red-500 focus:border-red-500 focus:ring-red-500');
            fieldElement.removeClass('border-gray-300 focus:border-blue-500 focus:ring-blue-500');
        }
        
        // Add error message
        const errorHtml = `<p class="mt-1 text-sm text-red-600 validation-error">${message}</p>`;
        
        if (field === 'tags' || field === 'locations') {
            fieldElement.closest('.select2-container').after(errorHtml);
        } else {
            fieldElement.after(errorHtml);
        }
    }

    hideError(field) {
        const fieldElement = this.getFieldElement(field);
        if (!fieldElement) return;

        // Remove error styling
        if (field === 'tags' || field === 'locations') {
            // Style Select2 container
            fieldElement.removeClass('border-red-500');
            fieldElement.addClass('border-gray-300');
            // Remove error message for Select2
            fieldElement.closest('.select2-container').siblings('.validation-error').remove();
        } else {
            fieldElement.removeClass('border-red-500 focus:border-red-500 focus:ring-red-500');
            fieldElement.addClass('border-gray-300 focus:border-blue-500 focus:ring-blue-500');
            // Remove error message for regular fields
            fieldElement.siblings('.validation-error').remove();
        }
    }

    getFieldElement(field) {
        switch(field) {
            case 'title':
                return this.form.find('input[name="title"]');
            case 'description':
                return this.form.find('textarea[name="description"]');
            case 'url':
                return this.form.find('input[name="url"]');
            case 'tags':
                return $('#tags').next('.select2-container').find('.select2-selection');
            case 'locations':
                return $('#location-select').next('.select2-container').find('.select2-selection');
            default:
                return this.form.find(`[name="${field}"]`);
        }
    }

    scrollToFirstError() {
        const firstErrorField = Object.keys(this.errors)[0];
        if (firstErrorField) {
            const element = this.getFieldElement(firstErrorField);
            if (element && element.length) {
                $('html, body').animate({
                    scrollTop: element.offset().top - 100
                }, 500);
                element.focus();
            }
        }
    }

    updateSubmitButton() {
        const submitBtn = $('#submit-btn');
        const hasErrors = Object.keys(this.errors).length > 0;
        
        if (hasErrors) {
            submitBtn.prop('disabled', true);
            submitBtn.addClass('disabled:bg-gray-400 disabled:cursor-not-allowed');
            submitBtn.attr('title', 'Please fix validation errors before submitting');
        } else {
            submitBtn.prop('disabled', false);
            submitBtn.removeClass('disabled:bg-gray-400 disabled:cursor-not-allowed');
            submitBtn.removeAttr('title');
        }
    }

    showGlobalErrorMessage() {
        // Remove existing global error
        $('.global-validation-error').remove();
        
        const errorCount = Object.keys(this.errors).length;
        const errorMessage = `Please fix ${errorCount} validation error${errorCount > 1 ? 's' : ''} before submitting the form.`;
        
        const errorHtml = `
            <div class="global-validation-error bg-red-50 border border-red-200 rounded-md p-4 mb-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Form Validation Errors</h3>
                        <p class="text-sm text-red-700 mt-1">${errorMessage}</p>
                    </div>
                </div>
            </div>
        `;
        
        this.form.prepend(errorHtml);
    }
}

// Initialize validator
const newsValidator = new NewsFormValidator();

// Original Select2 and Maps initialization
$(document).ready(function() {
    $('#tags').select2({
        tags: true,
        tokenSeparators: [',', ' '],
        ajax: {
            url: '{{ route('admin.news.search-tags') }}',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data.map(function(item) {
                        return {
                            id: item.name,
                            text: item.name
                        };
                    })
                };
            }
        }
    });

    $('#location-select').select2({
        placeholder: 'Search for a location...',
        minimumInputLength: 3,
        ajax: {
            transport: function (params, success, failure) {
                let service = new google.maps.places.AutocompleteService();
                service.getPlacePredictions({ input: params.data.term, types: ['(regions)'] }, function(predictions, status) {
                    if (status === google.maps.places.PlacesServiceStatus.OK) {
                        success(predictions.map(function(prediction) {
                            return { id: prediction.place_id, text: prediction.description };
                        }));
                    } else {
                        failure();
                    }
                });
            },
            processResults: function(data) {
                return {
                    results: data
                };
            }
        }
    });

    $('.location-type-btn').on('click', function() {
        $('.location-type-btn').removeClass('active');
        $(this).addClass('active');
        $('.location-section').addClass('hidden');
        if ($(this).data('type') === 'map') {
            $('#map').removeClass('hidden');
        } else {
            $('#region-location').removeClass('hidden');
        }
    });
});
</script>
@endpush