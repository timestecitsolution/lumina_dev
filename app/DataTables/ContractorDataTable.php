<?php

namespace App\DataTables;
use App\Helper\Common;
use App\Models\Contractor;
use Yajra\DataTables\Html\Column;

class ContractorDataTable extends BaseDataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('contractor_type', fn($row) => $row->type->type_name ?? '-')
            ->editColumn('status', function ($row) {
                $checked = $row->status == 1 ? 'checked' : '';
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
            ->addColumn('action', function ($row) {
                $edit = '<a href="'.route('contractors.edit', $row->id).'" class="btn btn-warning btn-sm mr-1">Edit</a>';
                $delete = '<button class="btn btn-danger btn-sm delete-row" data-url="'.route('contractors.destroy', $row->id).'">Delete</button>';
                $show = '<a href="'.route('contractors.show', $row->id).'" class="btn btn-info btn-sm mr-1">Show</a>';
                return $show.$edit.$delete;
            })
            ->rawColumns(['status', 'action']);
    }

    public function query(Contractor $model)
    {
        $query = $model->with('type')->select('contractors.*');
        // Filters
        if (request()->has('status') && request('status') != 'all') {
            $query->where('status', request('status') == 1 ? 1 : 0);
        }
        if (request()->has('contractor_type') && request('contractor_type') != 'all') {
            $query->where('contractor_type_id', request('contractor_type'));
        }
        if (request()->has('searchText') && !empty(request('searchText'))) {
            $search = request('searchText');
            $query->where(function($q) use ($search){
                $q->where('name','like',"%$search%")
                  ->orWhere('phone','like',"%$search%");
            });
        }
        return $query;
    }

    public function html()
    {
        return $this->setBuilder('contractors-table', 3)
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["contractors-table"]
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
            ['data' => 'id', 'name' => 'id', 'title' => 'ID'],
            ['data' => 'name', 'name' => 'name', 'title' => 'Name'],
            ['data' => 'contractor_type', 'name' => 'type.type_name', 'title' => 'Contractor Type'],
            ['data' => 'phone', 'name' => 'phone', 'title' => 'Phone'],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status', 'orderable' => false, 'searchable' => false],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
        ];
    }
}
