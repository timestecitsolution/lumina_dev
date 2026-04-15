<?php

namespace App\DataTables;

use App\Helper\Common;
use App\Models\Section;
use Yajra\DataTables\Html\Column;

class SectionDataTable extends BaseDataTable
{
    private $managePermission;

    public function __construct()
    {
        parent::__construct();
        $this->managePermission = user()->permission('manage_sections');
    }

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)

            ->addColumn('project_name', function ($row) {
                return $row->project->project_name ?? '--';
            })

            ->addColumn('action', function ($row) {

                $buttons = '';


                    $buttons .= '<a href="' . route('sections.edit', $row->id) . '" 
                                    class="btn btn-sm btn-primary mr-1">
                                    <i class="fa fa-edit"></i>
                                </a>';

                    $buttons .= '<button
                                    class="btn btn-sm btn-danger delete-row"
                                    data-url="' . route('sections.destroy', $row->id) . '">
                                    <i class="fa fa-trash"></i>
                                </button>';
                

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
            ->setRowId(fn ($row) => 'row-' . $row->id)
            ->rawColumns(['status', 'action']);
    }

    public function query(Section $model)
    {
        

        $model = $model->with('project')   
            ->select(
                    'id',
                'project_id',
                'section_name',
                'status',
                'created_at'
            );

        if (request()->searchText != '') {
            $model->where(function ($q) {
                $safe = Common::safeString(request('searchText'));
                $q->where('section_name', 'like', "%{$safe}%")
                  ->orWhere('status', 'like', "%{$safe}%");
            });
        }

        if (request()->status && request()->status != 'all') {
            $model->where('status', request()->status);
        }

        if (request()->has('project') && request()->project != 'all') {
            $model->where('project_id', request()->project);
        }

        return $model;
    }

    public function html()
    {
        return $this->setBuilder('section-table', 3)
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["section-table"]
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

            __('app.id') => [
                'data' => 'id',
                'name' => 'id'
            ],

            __('modules.project.sectionName') => [
                'data' => 'section_name',
                'name' => 'section_name'
            ],

            __('modules.contractor.project') => [
                'data' => 'project_name',
                'name' => 'projects.project_name'
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
