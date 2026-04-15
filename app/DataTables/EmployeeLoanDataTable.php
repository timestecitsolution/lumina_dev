<?php
namespace App\DataTables;

use App\Models\EmployeeLoan;
use Yajra\DataTables\Html\Column;

class EmployeeLoanDataTable extends BaseDataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('employee_name', function ($row) {
                return $row->employee ? $row->employee->name : '-';
            })
            ->addColumn('action', function ($row) {
                return view('loans.actions', compact('row'));
            })
            ->editColumn('requested_amount', function ($row) {
                return number_format($row->requested_amount, 2);
            })
            ->editColumn('approved_amount', function ($row) {
                return $row->approved_amount ? number_format($row->approved_amount, 2) : '-';
            })
            ->editColumn('status', function ($row) {
                return ucfirst($row->status);
            })
            ->editColumn('start_deduction_date', function ($row) {
                return $row->start_deduction_date ? $row->start_deduction_date->format('Y-m-d') : '-';
            })
            ->rawColumns(['action']);
    }

    public function query(EmployeeLoan $model)
    {
        $query = $model->newQuery()->with('employee');

        // Server-side filter
        if(request()->has('status') && request('status') != 'all') {
            $query->where('status', request('status'));
        }

        if(request()->has('searchText') && request('searchText') != '') {
            $search = request('searchText');
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            })
            ->orWhere('loan_no', 'like', "%$search%");
        }

        return $query;
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('employee-loans-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row mb-3'<'col-md-6'l><'col-md-6'f>>rt<'row'<'col-md-6'i><'col-md-6'p>>")
            ->orderBy(1)
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'pageLength' => 25,
            ]);
    }

    protected function getColumns()
    {
        return [
            Column::make('id')->title('#')->addClass('text-center'),
            Column::make('loan_no')->title('Loan No'),
            Column::make('employee_name')->title('Employee'),
            Column::make('requested_amount')->title('Requested Amount'),
            Column::make('approved_amount')->title('Approved Amount'),
            Column::make('status')->title('Status'),
            Column::make('start_deduction_date')->title('Start Deduction'),
            Column::computed('action')
                ->title('Action')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'EmployeeLoans_' . date('YmdHis');
    }
}
