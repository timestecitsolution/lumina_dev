<?php

namespace App\DataTables;
use App\Models\Deed;
use Yajra\DataTables\Html\Column;
use App\Helper\Common;

class DeedDataTable extends BaseDataTable
{
    private $managePermission;

    public function __construct()
    {
        parent::__construct();
        $this->managePermission = user()->permission('manage_deeds');
    }

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)

            ->addColumn('project', function ($row) {
                return $row->project->project_name ?? '-';
            })

            ->addColumn('contractor', function ($row) {
                return $row->contractor->name ?? '-';
            })

            ->addColumn('action', function ($row) {

                $buttons = '';

                // if ($this->managePermission == 'all') {

                    $buttons .= '<a href="'. route('deeds.show', $row->id).'" class="btn btn-sm btn-info  mr-1"><i class="fa fa-eye"></i> </a>';

                    $buttons .= '<a href="' . route('deeds.edit', $row->id) . '" 
                        class="btn btn-sm btn-success mr-1"><i class="fa fa-pen"></i></a>';

                    $buttons .= '<button class="btn btn-sm btn-warning delete-row"
                        data-url="' . route('deeds.destroy', $row->id) . '">
                        <i class="fa fa-trash"></i> </button>';
                // }

                return $buttons;
            })

            ->editColumn('status', function ($row) {

                $checked = $row->status === 'Yes' ? 'checked' : '';

                return '
                    <div class="hk-toggle hk-tg-3">
                        <input type="checkbox"
                               id="tg-'.$row->id.'"
                               class="toggle-status"
                               data-id="'.$row->id.'"
                               '.$checked.'>
                        <label for="tg-'.$row->id.'"></label>
                    </div>
                ';
            })

            ->editColumn('deed_total_amount', function ($row) {
                return number_format($row->deed_total_amount, 2);
            })

            ->addIndexColumn()
            ->setRowId(fn($row) => 'row-' . $row->id)
            ->rawColumns(['status', 'action']);
    }

    public function query(Deed $model)
    {
        $model = $model->with(['project','contractor'])
            ->select(
                'id',
                'deed_name',
                'project_id',
                'contractor_id',
                'deed_total_amount',
                'deed_date',
                'status',
                'created_at'
            );

        // Search
        if (request()->searchText != '') {
            $model->where(function ($q) {
                $safe = Common::safeString(request('searchText'));
                $q->where('deed_name', 'like', "%{$safe}%");
            });
        }

        // Status filter
        if (request()->status && request()->status != 'all') {
            $model->where('status', request()->status);
        }

        return $model;
    }

    public function html()
    {
        return $this->setBuilder('deed-table', 6)
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["deed-table"]
                        .buttons().container()
                        .appendTo("#table-actions");
                }',
                'fnDrawCallback' => 'function() {
                    $(".change-status").selectpicker();
                }',
            ]);
    }

    protected function getColumns()
    {
        return [
            __('app.id') => ['data' => 'id', 'name' => 'id'],

            'Deed Name' => [
                'data' => 'deed_name',
                'name' => 'deed_name'
            ],

            'Deed Date' => [
                'data' => 'deed_date',
                'name' => 'deed_date'
            ],
            

            'Project' => [
                'data' => 'project',
                'name' => 'project.project_name'
            ],

            'Contractor' => [
                'data' => 'contractor',
                'name' => 'contractor.contractor_name'
            ],

            'Total Amount' => [
                'data' => 'deed_total_amount',
                'name' => 'deed_total_amount'
            ],

            Column::computed('status', __('app.status'))
                ->exportable(false)
                ->width(150),

            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20'),
        ];
    }
}
