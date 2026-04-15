<?php
namespace App\DataTables;

use App\Models\Investment;
use Yajra\DataTables\Html\Column;

class InvestmentDataTable extends BaseDataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('bank_name', function ($row) {
                return $row->bank ? $row->bank->bank_name.' - ('.$row->bank->account_number.')' : '-';
            })
            ->addColumn('action', function ($row) {
                return view('investments.actions', compact('row'));
            })
            ->editColumn('amount', function ($row) {
                return number_format($row->amount, 2);
            })
            ->editColumn('transaction_type', function ($row) {
                return strtoupper($row->transaction_type);
            })
            ->rawColumns(['action']);
    }

    public function query(Investment $model)
    {
        return $model->newQuery()->with('bank');
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('investments-table')
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
            Column::make('investment_name')->title('Investment Name'),
            Column::make('amount')->title('Amount'),
            Column::make('transaction_type')->title('Type'),
            Column::make('bank_name')->title('Bank'),
            Column::make('note')->title('Note'),
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
        return 'Investments_' . date('YmdHis');
    }
}
