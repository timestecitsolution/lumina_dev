<?php

namespace App\DataTables;

use App\Helper\Common;
use App\Models\Step;
use Yajra\DataTables\Html\Column;

class StepDataTable extends BaseDataTable
{
    private $managePermission;

    public function __construct()
    {
        parent::__construct();
        $this->managePermission = user()->permission('manage_steps');
    }

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)

            ->addColumn('project', fn($row) => $row->project->project_name ?? '--')
            ->addColumn('section', fn($row) => $row->section->section_name ?? '--')
            
            ->editColumn('status', function ($row) {
                $statuses = ['Pending', 'On Process', 'Finished'];
                $checkedClass = '';
                $label = $statuses[$row->status] ?? '--';

                // toggle checkbox
                return $label;
            })

            ->addColumn('action', function ($row) {
                $buttons = '';

                $buttons .= '<a href="'.route('steps.edit', $row->id).'" class="btn btn-sm btn-primary mr-1">
                                <i class="fa fa-edit"></i>
                            </a>';

                $buttons .= '<button
                                class="btn btn-sm btn-danger delete-row"
                                data-url="'.route('steps.destroy', $row->id).'">
                                <i class="fa fa-trash"></i>
                            </button>';

                return $buttons;
            })

            ->addIndexColumn()
            ->setRowId(fn($row) => 'row-' . $row->id)
            ->rawColumns(['status','action']);
    }

    public function query(Step $model)
    {
        $model = $model->with('project','section')
                       ->select('id','project_id','section_id','step_name','status','created_at');

        if (request()->searchText != '') {
            $safe = Common::safeString(request('searchText'));
            $model->where(function($q) use ($safe){
                $q->where('step_name','like',"%$safe%")
                  ->orWhere('status','like',"%$safe%");
            });
        }

        if (request()->status && request()->status != 'all') {
            $model->where('status', request()->status);
        }

        if (request()->has('project') && request()->project != 'all') {
            $model->where('project_id', request()->project);
        }

        if (request()->has('section') && request()->section != 'all') {
            $model->where('section_id', request()->section);
        }

        return $model;
    }

    public function html()
    {
        return $this->setBuilder('step-table', 4)
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["step-table"]
                        .buttons().container()
                        .appendTo("#table-actions");
                }',
                'fnDrawCallback' => 'function() {
                    $(".toggle-status").change(function () {
                        var id = $(this).data("id");
                        $.easyAjax({
                            url: "'.url('account/steps').'/"+id+"/toggle-status",
                            type: "POST",
                            data: {_token: csrfToken}
                        });
                    });
                }',
            ]);
    }

    protected function getColumns()
    {
        return [
            __('app.id') => ['data'=>'id','name'=>'id'],
            __('modules.project.project') => ['data'=>'project','name'=>'projects.project_name'],
            __('modules.project.section') => ['data'=>'section','name'=>'sections.section_name'],
            __('modules.project.stepName') => ['data'=>'step_name','name'=>'step_name'],
            Column::computed('status', __('app.status'))->exportable(false)->width(150),
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20'),
        ];
    }
}
