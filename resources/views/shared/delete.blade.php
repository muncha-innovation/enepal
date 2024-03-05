<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).on("click", ".delete", function(e) {
        e.preventDefault();
        e.stopPropagation();
        var route = $(this).attr('href');
        var token = $("meta[name='csrf-token']").attr("content");
        Swal.fire({
            title: "{{ trans('Are you sure?') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: " {{ trans('Cancel') }} ",
            confirmButtonText: "{{ trans('Yes, Delete it!') }}"
        }).then((result) => {
            if (result.value === true) {
                $.ajax({
                    url: route,
                    type: 'DELETE',
                    data: {
                        "_token": token,
                    },
                    success: function(data) {
                        if (data.location) {
                            window.location.href = data.location
                        } else {
                            window.location.reload();
                        }
                    },
                    error: function(data) {
                        if (data.responseJSON.message) {
                            Swal.fire({
                                title: data.responseJSON.message,
                            })
                        }

                    }
                })
            }
        })
    });
</script>
