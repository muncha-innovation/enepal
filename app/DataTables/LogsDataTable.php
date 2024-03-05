<?php

namespace App\DataTables;


use App\Models\LogTypes;
use App\Services\DateFormatter;
use App\Services\LogFormatter;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Services\DataTable;

class LogsDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->editColumn('created_at', function ($result) {
                return DateFormatter::utcToJp($result->created_at);
            })
            ->addColumn('user', function ($result) {
                return $result->causer?->full_name;
            })
            ->editColumn('description', function ($result) {
                return LogFormatter::format($result);
            })
            ->addColumn('query', function ($result) {
                return $result->getExtraProperty('search_query');
            });
    }


    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Logs $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Activity $model)
    {
        $model = $model->newQuery();
        return $model->whereBetween('created_at', [$this->startDate, $this->endDate])->orderBy('created_at', 'DESC');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('logss-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->buttons(
                Button::make('reload'),
            )
            ->parameters([
                'language' => ['url' => asset('/trans/js/' . app()->getLocale() . '/lang.json')],
            ])->responsive(true);
        ;
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        // Translation complete

        return [
            Column::make('description')->title(__('Description'))->sortable(false),
            Column::make('user')->title(__('User'))->sortable(false),
            Column::make('query')->title(__('Query'))->sortable(false),
            Column::make('created_at')->title(__('Created At')),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return trans('Logss') . '_' . date('YmdHis');
    }
}