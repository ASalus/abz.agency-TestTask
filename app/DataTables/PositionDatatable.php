<?php

namespace App\DataTables;

use App\Models\Position;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class PositionDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return dataTables()
            ->eloquent($query)
            ->addColumn( 'action', function ( $query ) {
                $button = '<a class="pe-auto editPosition" positionId="'.$query->id.'" title="Edit('.$query->id.')" id="editPositionBtn" data-bs-whatever="@edit"><i class="fa fa-edit"></i></a>';
                $button .= '&nbsp;';
                $button .= '<a class="pe-auto delete" id="positionDelete" data-id="'.$query->id.'" title="Delete('.$query->id.')"><i class="fa fa-trash"></i></a>';
                return $button;
            } )
            ->editColumn('updated_at', function(Position $position) {
                
                return $position->updated_at->format('d.m.y');
            })
            ;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Position $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Position $model)
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
                    ->setTableId('position-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ;
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('position_name'),
            Column::make('updated_at'),
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
        return 'Position_' . date('YmdHis');
    }
}
