<?php

namespace App\DataTables;

use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\Models\Employee;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use App\Models\Position;
use DateTime;
use Illuminate\Support\Facades\Request;

class EmployeeDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query, Request $request)
    {
        //$model = Employee::with('position');
        return dataTables()
            ->eloquent($query)
            ->addColumn('photo', function ($query) {
                $html = '<img class="pic-table" src="/images/' . $query->image . '"/>';
                return  $html;
            })
            ->addColumn('position', function ($query) {
                return $query->position->position_name;
            })
            ->addColumn('action', function ($query) {
                $button = '<a class="pointer edit" data-id="' . $query->id . '" title="Edit(' . $query->id . ')" id="editButton"><i class="fa fa-edit"></i></a>';
                $button .= '&nbsp;';
                $button .= '<a class="pointer delete" data-id="' . $query->id . '" title="Delete(' . $query->id . ')" id="deleteEmployee"><i class="fa fa-trash"></i></a>';
                return $button;
            })
            ->editColumn('employment_date', function (Employee $employee) {
                $date = DateTime::createFromFormat('Y-m-d', $employee->employment_date);
                return $date->format('d.m.y');
            })
            ->rawColumns(['photo', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Employee $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Employee $model)
    {
        return $model->newQuery()->with(['position']);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('employees-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters([
                'order' => [[3, 'desc']],
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('photo'),
            Column::make('name'),
            Column::make('position', 'position.position_name'),
            Column::make('employment_date'),
            Column::make('phone_number'),
            Column::make('email'),
            Column::make('salary'),
            Column::make('action'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Employee_' . date('YmdHis');
    }
}
