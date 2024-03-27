<?php

namespace App\DataTables;

use App\Models\User;
use App\Services\DateFormatter;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
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


            ->addColumn('user_type', function ($result) {
                return trans($result->getRoleNames()[0]);
            })
            ->addColumn('action', function ($result) {
                $userId = $result->id;
                return view('modules.users.partials.actions', [
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
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
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
            ->setTableId('users-table')
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

            Column::make('id')->title(__('Id')),
            Column::make('user_name')->title(__('User Name')),
            Column::make('name')->title(__('Name')),
            Column::make('user_type')->title(__('User Type'))->sortable(false),
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
        return trans('Users') . '_' . date('YmdHis');
    }
}