<?php

namespace App\DataTables;

use App\Models\Investor;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class InvestorDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($row) {
                $edit = '<a href="'.route('investors.edit', $row->id).'" class="btn btn-sm btn-primary mr-1">Edit</a>';

                $delete = '<form method="POST" action="'.route('investors.destroy', $row->id).'" style="display:inline-block" onsubmit="return confirm(\'Are you sure?\')">
                            '.csrf_field().method_field('DELETE').'
                            <button class="btn btn-sm btn-danger">Delete</button>
                           </form>';

                return $edit . $delete;
            })
            ->addColumn('assigned', function ($row) {
                return $row->assigned_employee_from_investor ? 'Yes' : 'No';
            })
            ->rawColumns(['action']);
    }

    public function query(Investor $model)
    {
        $searchText = request('searchText');

        $query = $model->newQuery();

        if ($searchText) {
            $query->where(function($qry) use ($searchText) {
                $qry->where('name', 'like', "%$searchText%")
                    ->orWhere('email', 'like', "%$searchText%")
                    ->orWhere('phone', 'like', "%$searchText%")
                    ->orWhere('company', 'like', "%$searchText%");
            });
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('investors-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'desc')
            ->responsive(true)
            ->serverSide(true)
            ->processing(true)
            ->dom("<'row mb-3'<'col-sm-12'tr>>" .
                  "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>");
    }

    protected function getColumns()
    {
        return [
            Column::make('id')->title('#'),
            Column::make('name'),
            Column::make('company'),
            Column::make('phone'),
            Column::make('email'),
            Column::make('assigned')->title('Assigned Employee?'),
            Column::computed('action')->exportable(false)->printable(false)->width(120),
        ];
    }
}
