<?php

namespace App\DataTables;

use App\Models\ContractorType;
use Yajra\DataTables\Html\Column;
use App\Helper\Common;

class ContractorTypeDataTable extends BaseDataTable
{
    private $managePermission;

    public function __construct()
    {
        parent::__construct();
        $this->managePermission = user()->permission('manage_contractor_type');
    }

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($row) {
                $buttons = '';
                $buttons .= '<a href="'.route('contractor-types.edit', $row->id).'" class="btn btn-sm btn-primary mr-1"><i class="fa fa-edit"></i></a>';
                $buttons .= '<button class="btn btn-sm btn-danger delete-row" data-url="'.route('contractor-types.destroy', $row->id).'"><i class="fa fa-trash"></i></button>';
                return $buttons;
            })
            ->editColumn('status', function ($row) {

                $checked = $row->status === 'yes' ? 'checked' : '';

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
            ->addIndexColumn()
            ->setRowId(fn($row) => 'row-' . $row->id)
            ->rawColumns(['status', 'action']);
    }

    public function query(ContractorType $model)
    {
        $model = $model->select('id','type_name','status','created_at');

        if (request()->searchText != '') {
            $model->where(function ($q) {
                $safe = Common::safeString(request('searchText'));
                $q->where('type_name', 'like', "%{$safe}%")
                  ->orWhere('status', 'like', "%{$safe}%");
            });
        }

        if (request()->status && request()->status != 'all') {
            $model->where('status', request()->status);
        }

        return $model;
    }

    public function html()
    {
        return $this->setBuilder('contractor-type-table', 3)
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["contractor-type-table"]
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
            __('modules.contractor.typeName') => ['data' => 'type_name', 'name' => 'type_name'],
            Column::computed('status', __('app.status'))->exportable(false)->width(150),
            Column::computed('action', __('app.action'))->exportable(false)->printable(false)->orderable(false)->searchable(false)->addClass('text-right pr-20'),
        ];
    }
}
