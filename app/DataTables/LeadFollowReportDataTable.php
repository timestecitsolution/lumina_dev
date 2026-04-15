<?php

namespace App\DataTables;

use App\Models\LeadAgent;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Button;

class LeadFollowReportDataTable extends BaseDataTable
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

            ->addColumn('note_details', function ($row) {
                if (!$row->note) {
                    return '-';
                }

                $text = strip_tags($row->note); // HTML remove
                return \Illuminate\Support\Str::limit($text, 120);
            })

            
            ->rawColumns(['action','note_details']);
    }


    /**
     * @param LeadAgent $model
     * @return \Illuminate\Database\Eloquent\Builder
     */


public function query()
{
    $request = $this->request();

    $query = DB::table('lead_notes')
        ->join('leads', 'leads.id', '=', 'lead_notes.lead_id')
        ->join('users', 'users.id', '=', 'leads.lead_owner')

        ->select(
            'lead_notes.id as note_id',
            'lead_notes.details as note',
            'lead_notes.note_date as visit_date',
            'lead_notes.next_date as next_follow_up_date',
            'leads.id as lead_id',
            'leads.client_name',
            'leads.mobile',
            'leads.address',
            'leads.lead_owner',

            'users.name as lead_owner_name',

            // ✅ SAFE project fetch (NO JOIN)
            DB::raw("(
                SELECT GROUP_CONCAT(projects.project_name SEPARATOR ', ')
                FROM projects
                WHERE JSON_CONTAINS(
                    leads.project_id,
                    CONCAT('\"', projects.id, '\"')
                )
            ) as project_name")
        );

    // Date filter
    if (filled($request->startDate) && filled($request->endDate)) {
        $query->where(function ($q) use ($request) {
            $q->whereBetween(
                DB::raw("STR_TO_DATE(lead_notes.note_date, '%d-%m-%Y')"),
                [
                    companyToDateString($request->startDate),
                    companyToDateString($request->endDate)
                ]
            )->orWhereNull('lead_notes.note_date')
             ->orWhere('lead_notes.note_date', '');
        });
    }

    // Agent filter
    if (filled($request->agent) && $request->agent !== 'all') {
        $query->where('leads.lead_owner', $request->agent);
    }

    // Lead filter
    if (filled($request->lead) && $request->lead !== 'all') {
        $query->where('lead_notes.lead_id', $request->lead);
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
        $dataTable = $this->setBuilder('lead-follow-table', 5)
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["lead-follow-table"].buttons().container()
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
                'name' => 'project_name'
            ],

            __('modules.lead.noteDetails') => [
                'data' => 'note_details',
                'orderable' => false,
                'searchable' => false
            ],

            __('modules.lead.visitDate') => [
                'data' => 'visit_date',
                'name' => 'lead_notes.note_date'
            ],

            __('modules.lead.nextFollowUp') => [
                'data' => 'next_follow_up_date',
                'name' => 'lead_notes.next_date'
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
