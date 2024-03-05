<?php

namespace App\DataTables;

use App\Models\Checklist;
use App\Services\DateFormatter;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ChecklistDataTable extends DataTable
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
            // ->addColumn('code', function ($result) {
            //     return $result->code;
            // })
            // ->addColumn('title', function ($result) {
            //     return $result->title;
            // })
            ->addColumn('machine_name', function($result) {
                return $result->machine->name;
            })
            ->addColumn('action', function ($result) {
                $userId = $result->id;
                return view('shared.actions', [
                    'viewRoute' => route('users.show', $userId),
                    'editRoute' => route('users.edit', $userId),
                    'deleteRoute' => route('users.destroy', $userId),
                    // 'logRoute' => route('user.logs', ['userId' => $userId])
                ]);
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Checklist $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Checklist $model)
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('checklist-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->buttons(
                Button::make('create'),
                Button::make('export'),
                Button::make('print'),
                Button::make('reload')
            )->parameters([
                'language' => ['url' => asset('/trans/js/' . app()->getLocale() . '/lang.json')],
            ])
            ->responsive(true);
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
            Column::make('code')->title(__('Checklist No.')),
            Column::make('title')->title(__('Title')),
            Column::make('machine_name')->title(__('Name of machinery/equipment')),

            Column::make('created_at')->title(__('Created At')),
            Column::computed('action')
                ->title(__('Action'))
                ->exportable(false)
                ->printable(false)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return trans('Checklist') . '_' . date('YmdHis');
    }
}