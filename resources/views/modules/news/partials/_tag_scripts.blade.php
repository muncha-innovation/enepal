<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script>
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
                console.log(data);
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
});
</script>