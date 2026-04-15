<?php

namespace Modules\Purchase\DataTables;

use Carbon\Carbon;
use App\DataTables\BaseDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\Purchase\Entities\Requisition;

class RequisitionDataTable extends BaseDataTable
{
    private $viewPermission;

    public function __construct()
    {
        parent::__construct();
        $this->viewPermission = user()->permission('view_requisition');
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($row) {

                $user = user();
                $isSuperAdmin = ($user->id == 1);
                $isOwner = ($row->requested_by == $user->id);
                $canModify = ($isSuperAdmin || $isOwner);


                $action = '<div class="task_view">
                    <div class="dropdown">
                        <a class="task_view_more dropdown-toggle" data-toggle="dropdown">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">';
                
                    $action .= '<a href="'.route('requisitions.show', $row->id).'"
                        class="dropdown-item">
                        <i class="fa fa-eye mr-2"></i>'.__('app.view').'</a>';

                    if ($canModify && in_array($row->status, ['pending', 'rejected'])) {
                        $action .= '<a href="'.route('requisitions.edit', $row->id).'"
                            class="dropdown-item">
                            <i class="fa fa-edit mr-2"></i>'.__('app.edit').'</a>';
                    }

                    
                    if ($canModify && $row->status !== 'approved') {
                        $action .= '<a href="'.route('requisitions.destroy', $row->id).'"
                            class="dropdown-item delete-table-row">
                            <i class="fa fa-trash mr-2"></i>'.__('app.delete').'</a>';
                    }

                $action .= '</div></div></div>';

                return $action;
            })
            ->addColumn('req_date', fn($row) =>
                Carbon::parse($row->req_date)
                    ->timezone($this->company->timezone)
                    ->translatedFormat($this->company->date_format)
            )
            ->addColumn('requested_by', fn($row) =>
                optional($row->requestedBy)->name
            )
            ->addColumn('project', fn($row) =>
                optional($row->project)->project_name
            )
            ->addColumn('delivery_date', fn($row) => $row->delivery_date ?? '--')
            ->addColumn('total_items', fn($row) =>
                $row->items_count
            )
            ->addColumn('status', function ($row) {
                $color = match ($row->status) {
                    'approved' => 'text-dark-green',
                    'rejected' => 'text-red',
                    'pending'  => 'text-yellow',
                    default    => 'text-grey',
                };

                return '<i class="fa fa-circle '.$color.' f-10 mr-1"></i>'
                    .__('app.'.$row->status);
            })
            ->addIndexColumn()
            ->setRowId(fn($row) => 'row-'.$row->id)
            ->rawColumns(['status', 'action']);
    }

    public function query(): QueryBuilder
    {
        $request = $this->request();

        $model = Requisition::with(['requestedBy', 'approvedBy','project'])
            ->withCount('items');

        if ($request->startDate) {
            $start = Carbon::createFromFormat(
                $this->company->date_format,
                $request->startDate
            )->toDateString();

            $model->whereDate('req_date', '>=', $start);
        }

        if ($request->endDate) {
            $end = Carbon::createFromFormat(
                $this->company->date_format,
                $request->endDate
            )->toDateString();

            $model->whereDate('req_date', '<=', $end);
        }

        if ($request->status && $request->status !== 'all') {
            $model->where('status', $request->status);
        }

        if ($this->viewPermission == 'added') {
            $model->where('requested_by', user()->id);
        }

        if ($request->searchText) {
            $model->where(function ($q) use ($request) {
                $q->where('req_no', 'like', "%{$request->searchText}%")
                  ->orWhere('delivery_place', 'like', "%{$request->searchText}%")
                  ->orWhereHas('project', function ($u) use ($request) {
                      $u->where('project_name', 'like', "%{$request->searchText}%");
                  })
                  ->orWhereHas('requestedBy', function ($u) use ($request) {
                      $u->where('name', 'like', "%{$request->searchText}%");
                  });
            });
        }

        return $model;
    }

    public function html(): HtmlBuilder
    {

        return parent::setBuilder('requisitions-table', 1)
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["requisitions-table"].buttons().container()
                     .appendTo( "#table-actions")
                 }',
                'fnDrawCallback' => 'function( oSettings ) {
                   $(".select-picker").selectpicker();
                 }',
            ]);
    }

    public function getColumns(): array
    {
        return [
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false],
            __('app.reqNo') => ['data' => 'req_no', 'name' => 'req_no'],
            __('app.project') => ['data' => 'project', 'name' => 'project.project_name'],
            __('app.reqDate') => ['data' => 'req_date', 'name' => 'req_date'],
            __('app.requestedBy') => ['data' => 'requested_by', 'name' => 'requestedBy.name'],
            __('app.project') => ['data' => 'project', 'name' => 'project.project_name'],
            __('app.deliveryDate') => ['data' => 'delivery_date', 'name' => 'delivery_date'],
            __('app.totalItems') => ['data' => 'total_items', 'orderable' => false],
            __('app.status') => ['data' => 'status', 'name' => 'status'],
            Column::computed('action', __('app.action'))
                ->orderable(false)
                ->searchable(false)
        ];
    }

    protected function filename(): string
    {
        return 'Requisitions_'.date('YmdHis');
    }
}
