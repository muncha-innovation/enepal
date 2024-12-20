<script>
async function uploadImage(input) {
    const file = input.files[0];
    if (!file) return;

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
            document.getElementById('image-url-input').value = data.url;

            const previewContainer = document.getElementById('image-preview-container');
            const existingPreview = document.getElementById('image-preview');

            if (existingPreview) {
                existingPreview.src = data.url;
            } else {
                previewContainer.innerHTML = `<img src="${data.url}" id="image-preview" alt="Preview" class="w-48 h-32 object-cover rounded-md">`;
            }
        }
    } catch (error) {
        console.error('Upload failed:', error);
        alert('Failed to upload image. Please try again.');
    }
}
</script> 