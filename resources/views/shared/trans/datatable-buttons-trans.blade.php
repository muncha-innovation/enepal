<script>
    (function ($, DataTable) {
    "use strict";
    DataTable.ext.buttons.create.text = function (dt) {
        return '<i class="fa fa-plus"></i> ' + dt.i18n('buttons.create', '{{ __('Create') }}');
    };

    DataTable.ext.buttons.excel.text = function (dt) {
        return '<i class="fa fa-file-excel-o"></i> ' + dt.i18n('buttons.excel', '{{ __('excel') }}');
    };

    DataTable.ext.buttons.pdf.text = function (dt) {
        return '<i class="fa fa-file-pdf-o"></i> ' + dt.i18n('buttons.pdf', '{{ __('pdf') }}');
    };

    DataTable.ext.buttons.csv.text = function (dt) {
                return '<i class="fa fa-file"></i> ' + dt.i18n('buttons.csv', '{{ __('csv') }}');
    };

    DataTable.ext.buttons.export.text = function (dt) {
        return '<i class="fa fa-download"></i> ' + dt.i18n('buttons.export', '{{ __('export') }}');
    };

    DataTable.ext.buttons.print.text = function (dt) {
        return '<i class="fa fa-print"></i> ' + dt.i18n('buttons.print', '{{ __('print') }}');
    };

    DataTable.ext.buttons.reset.text = function (dt) {
        return '<i class="fa fa-undo"></i> ' + dt.i18n('buttons.reset', '{{ __('reset') }}');
    };

    DataTable.ext.buttons.reload.text = function (dt) {
        return '<i class="fa fa-refresh"></i> ' + dt.i18n('buttons.reload', '{{ __('reload') }}');
    };
})(jQuery, jQuery.fn.dataTable);
</script>
