<script>
async function uploadImage(input) {
    const file = input.files[0];
    if (!file) return;

    // Show loading state
    const previewContainer = document.getElementById('image-preview-container');
    previewContainer.innerHTML = '<div class="text-center py-4"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div><p class="text-sm text-gray-600 mt-2">Uploading...</p></div>';

    const formData = new FormData();
    formData.append('image', file);

    try {
        const response = await fetch('{{ route('admin.news.upload-image') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (!response.ok) {
            throw new Error('Upload failed');
        }

        const data = await response.json();
        if (data.url) {
            // Set the URL in the hidden input
            document.getElementById('image-url-input').value = data.url;

            // Show preview
            const existingPreview = document.getElementById('image-preview');
            if (existingPreview) {
                existingPreview.src = data.url;
            } else {
                previewContainer.innerHTML = `<img src="${data.url}" id="image-preview" alt="Preview" class="rounded-lg border border-gray-200 object-cover" style="width: 200px; aspect-ratio: 16 / 9;">`;
            }
        }
    } catch (error) {
        console.error('Upload failed:', error);
        previewContainer.innerHTML = '<div class="text-center py-4 text-red-500"><p>Upload failed. Please try again.</p></div>';
        
        // Clear the file input
        input.value = '';
    }
}
</script> 