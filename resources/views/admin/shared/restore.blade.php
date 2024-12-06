<script>
    $(document).on("click", ".restore", function(e) {

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
            cancelButtonText: "{{ trans('Cancel') }}",
            confirmButtonText: "{{ trans('Yes, Restore it!') }}"
        }).then((result) => {
            if (result.value === true) {
                $.ajax({
                    url: route,
                    type: 'POST',
                    data: {
                        "_token": token,
                    },
                    success: function(data) {
                        window.location.reload();
                    }
                })
            }
        })

    });
</script>
