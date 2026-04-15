<?php

namespace App\DataTables;

use App\Models\LeadAgent;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Button;

class LeadNoteReportDataTable extends BaseDataTable
{

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */

    public function dataTable($query)
    {
        return datatables()
            ->query($query)
            ->addIndexColumn()
            ->addColumn('project_name', fn($row) => $row->project_name ?? '-')
            ->addColumn('lead_owner', fn ($row) => $row->lead_owner_name)
            ->addColumn('client_name', fn ($row) => $row->client_name)
            ->addColumn('phone', fn ($row) => $row->mobile)
            ->addColumn('address', fn ($row) => $row->address)
            ->addColumn('next_follow_up', fn ($row) => $row->next_follow_up_date ?? '-')

            ->addColumn('action', function ($row) {
                $note = url('account/lead-notes/' . $row->note_id); 
                $contact = url('account/lead-contact/' . $row->note_id); 

                return '<a href="'.$note.'" target="_blank"
                            class="btn btn-sm btn-primary">
                            <i class="fa fa-eye"></i> View Note
                        </a>
                        <a href="'.$contact.'" target="_blank"
                            class="btn btn-sm btn-primary">
                            <i class="fa fa-eye"></i> View Lead
                        </a>
                        ';
            })

            ->addColumn('visit_count', function($row) {
                $count = $row->visit_count ?? 0;
                return '<span class="badge rounded-circle bg-primary" style="
                            padding: 12px 16px; 
                            font-size: 16px; 
                            color: #ffffff;
                            display: inline-block;
                            min-width: 40px;
                            min-height: 40px;
                            text-align: center;
                            line-height: 1.2;
                        ">'.$count.'</span>';
            })
            ->rawColumns(['action', 'visit_count']);
    }


    /**
     * @param LeadAgent $model
     * @return \Illuminate\Database\Eloquent\Builder
     */


    public function queryOld()
    {
        $request = $this->request();

        $query = DB::table('leads')
            ->join('users', 'users.id', '=', 'leads.lead_owner')

            ->leftJoin('projects', function($join) {
                    $join->on(DB::raw("JSON_CONTAINS(leads.project_id, CAST(projects.id AS JSON))"), '=', DB::raw('1'));
                })

            ->join('lead_notes as ln', function ($join) {
                $join->on('ln.lead_id', '=', 'leads.id')
                     ->where('ln.visit_yes', 1)
                     ->whereRaw('ln.id = (
                        SELECT MAX(ln2.id)
                        FROM lead_notes ln2
                        WHERE ln2.lead_id = leads.id
                        AND ln2.visit_yes = 1
                     )');
            })

           
            ->leftJoinSub(
                DB::table('lead_notes as ln_count')
                    ->select('lead_id', DB::raw('COUNT(*) as visit_count'))
                    ->where('visit_yes', 1)
                    ->groupBy('lead_id'),
                'visit_count_sub',
                'visit_count_sub.lead_id',
                '=',
                'leads.id'
            )

            ->select(
                'leads.id',
                'leads.lead_owner',
                'ln.id as note_id',
                'users.name as lead_owner_name',
                'leads.client_name',
                'leads.mobile',
                'leads.address',
                'ln.next_date as next_follow_up_date',
                'ln.note_date as visit_date',
                'visit_count_sub.visit_count',
                DB::raw('GROUP_CONCAT(projects.project_name SEPARATOR ", ") as project_name')
            );


        
        if (filled($request->startDate) && filled($request->endDate)) {

            $startDate = companyToDateString($request->startDate);
            $endDate   = companyToDateString($request->endDate);

            if ($startDate && $endDate) {
                $query->whereBetween(
                    DB::raw("STR_TO_DATE(ln.note_date, '%d-%m-%Y')"),
                    [$startDate, $endDate]
                );
            }
        }

     
        if (filled($request->agent) && $request->agent !== 'all') {
          
            $query->where('leads.lead_owner', $request->agent);
        }
        
        
        return $query;
    }

    public function query()
    {
        $request = $this->request();

        $query = DB::table('leads')
            ->join('users', 'users.id', '=', 'leads.lead_owner')

            ->leftJoin('projects', function($join) {
                $join->on(DB::raw("JSON_CONTAINS(leads.project_id, CONCAT('\"', projects.id, '\"'))"), '=', DB::raw('1'));
            })



            ->join('lead_notes as ln', function ($join) {
                $join->on('ln.lead_id', '=', 'leads.id')
                     ->where('ln.visit_yes', 1)
                     ->whereRaw('ln.id = (
                        SELECT MAX(ln2.id)
                        FROM lead_notes ln2
                        WHERE ln2.lead_id = leads.id
                        AND ln2.visit_yes = 1
                     )');
            })

            ->leftJoinSub(
                DB::table('lead_notes as ln_count')
                    ->select('lead_id', DB::raw('COUNT(*) as visit_count'))
                    ->where('visit_yes', 1)
                    ->groupBy('lead_id'),
                'visit_count_sub',
                'visit_count_sub.lead_id',
                '=',
                'leads.id'
            )

            ->select(
                'leads.id',
                'leads.lead_owner',
                'ln.id as note_id',
                'users.name as lead_owner_name',
                'leads.client_name',
                'leads.mobile',
                'leads.address',
                'ln.next_date as next_follow_up_date',
                'ln.note_date as visit_date',
                'visit_count_sub.visit_count',
                DB::raw('GROUP_CONCAT(projects.project_name SEPARATOR ", ") as project_name')
            )
            ->groupBy('leads.id');

        if (filled($request->startDate) && filled($request->endDate)) {
            $startDate = companyToDateString($request->startDate);
            $endDate   = companyToDateString($request->endDate);

            if ($startDate && $endDate) {
                $query->whereBetween(
                    DB::raw("STR_TO_DATE(ln.note_date, '%d-%m-%Y')"),
                    [$startDate, $endDate]
                );
            }
        }

        if (filled($request->agent) && $request->agent !== 'all') {
            $query->where('leads.lead_owner', $request->agent);
        }

        return $query;
    }



    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        $dataTable = $this->setBuilder('lead-note-table', 5)
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["lead-note-table"].buttons().container()
                    .appendTo( "#table-actions")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                //
                $(".select-picker").selectpicker();
                }',
            ]);

        if (canDataTableExport()) {
            $dataTable->buttons(Button::make(['extend' => 'excel', 'text' => '<i class="fa fa-file-export"></i> ' . trans('app.exportExcel')]));
        }

        return $dataTable;
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'SN' => [
                'data' => 'DT_RowIndex',
                'orderable' => false,
                'searchable' => false
            ],

            

            __('modules.lead.leadOwner') => [
                'data' => 'lead_owner_name',
                'name' => 'users.name'
            ],

            __('modules.lead.clientName') => [
                'data' => 'client_name',
                'name' => 'leads.client_name'
            ],

            __('app.mobile') => [
                'data' => 'mobile',
                'name' => 'leads.mobile'
            ],

            __('app.address') => [
                'data' => 'address',
                'name' => 'leads.address'
            ],

            __('modules.lead.projectName') => [
                'data' => 'project_name',
                'name' => 'projects.project_name'
            ],

            __('modules.lead.visitDate') => [
                'data' => 'visit_date',
                'name' => 'ln.note_date'
            ],

            __('modules.lead.nextFollowUp') => [
                'data' => 'next_follow_up_date',
                'name' => 'ln.next_date'
            ],

            __('modules.lead.visitCount') => [
                'data' => 'visit_count',
                'orderable' => false,
                'searchable' => false
            ],
            

            __('app.action') => [
                'data' => 'action',
                'orderable' => false,
                'searchable' => false
            ],
        ];
    }



    public function pdf()
    {
        set_time_limit(0);

        if ('snappy' == config('datatables-buttons.pdf_generator', 'snappy')) {
            return $this->snappyPdf();
        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('datatables::print', ['data' => $this->getDataForPrint()]);

        return $pdf->download($this->getFilename() . '.pdf');
    }

}
