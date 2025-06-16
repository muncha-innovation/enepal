<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(function() {
        let counter = 0;
        let removeText = @json(trans('Remove'));
        var imagesPreview = function(input, placeToInsertImagePreview) {
            if (input.files) {
                var filesAmount = input.files.length;
                for (i = 0; i < filesAmount; i++) {
                    var file = input.files[i];
                    var name = input.files[i].name;
                    var splitName = name;
                    var extension = name.split('.').pop();
                    if (name.length > 15) var splitName = (input.files[i].name).substring(0, 15) + '...';
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        counter++;
                        var html = `
      <div class="group flex flex-col items-center gap-2 mb-4">
        <input type="hidden" name="images_name[${counter}]" value="${name}">
        <input type="hidden" name="images[${counter}]" value="${event.target.result}">
        
        <div class="w-24 h-24 rounded-lg">
          <img src="${extension=='pdf'?@json(asset('pfd_icon.png')):event.target.result}" alt="" class="w-full h-full rounded-lg object-cover">
        </div>

        <input type="text" name="images_title[${counter}]" class="form-input w-full border border-gray-300 rounded-md p-2 text-sm text-center" placeholder=@json(trans('Image caption'))>
        
        <button type="button" class="delete-image w-full px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
          ${removeText}
        </button>
      </div>
    `;
                        $(html).appendTo(placeToInsertImagePreview);
                    };

                    reader.readAsDataURL(input.files[i]);
                }
            }
        };

        $('#file-upload').on('click', function() {
            this.value = null;
        });
        $('#file-upload').on('change', function(e) {
            imagesPreview(this, 'div.gallery');
            // $('#mainImages').val($(this).val())
        });

        $(document).on('click', '.delete-image', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var token = $("meta[name='csrf-token']").attr("content");
            let deleteUrl = $(this).data('delete-url');

            Swal.fire({
                title: "{{ trans('Delete File?') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: "{{ trans('Cancel') }}",
                confirmButtonText: "{{ trans('Yes, Delete it!') }}"
            }).then((result) => {
                if (result.value === true) {
                    $(this).closest('.group').remove();

                    if (deleteUrl) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            data: {
                                "_token": token,
                            },
                            success: function(data) {
                                Swal.fire({
                                    title: data.message,
                                    timer: 1500,
                                    showConfirmButton: false,
                                })
                            },
                            error: function(data) {
                                Swal.fire({
                                    title: "{{ __('Something went wrong') }}",
                                    timer: 1500,
                                    showConfirmButton: false
                                })
                            }
                        })
                    }

                }
            });
        });

    });
</script>
