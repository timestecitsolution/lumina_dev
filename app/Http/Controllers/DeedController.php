<?php

namespace App\Http\Controllers;

use App\Models\Deed;
use App\Models\DeedDetail;
use App\Models\DeedDetailsStep;
use App\Models\Project;
use App\Models\Contractor;
use App\Models\Section;
use App\Models\Step;
use Illuminate\Http\Request;
use App\DataTables\DeedDataTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeedController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.deeds';
    }

    public function index(DeedDataTable $dataTable)
    {
        return $dataTable->render('deeds.index', $this->data);
    }

    public function create()
    {
        $this->pageTitle = "Add Deed";
        $this->projects = Project::all();
        $this->contractors = Contractor::with('type')->get();
        $this->sections = Section::all();
        $this->steps = Step::all();
        $this->view = 'deeds.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('deeds.create', $this->data);
    }

    public function store(Request $request)
    {

        $request->validate([
            'deed_name'      => 'required|string|max:255',
            'project_id'     => 'required|exists:projects,id',
            'contractor_id'  => 'required|exists:contractors,id',
            'deed_date'      => 'required|date',
            'deed_file'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',

            'sections'       => 'required|array|min:1',
            'sections.*.section_id'   => 'required|exists:sections,id',
            'sections.*.unit_type'    => 'required|string',
            'sections.*.per_unit_rate'=> 'required|numeric|min:0',
            'sections.*.total_unit'   => 'required|numeric|min:0',

            // Steps & percentages (current data is 2 separate arrays)
            'sections.*.steps'         => 'required|array|min:1',
            'sections.*.steps.*'       => 'required|exists:steps,id',  // just numeric ids
            'sections.*.step_percentage' => 'required|array|min:1',
            'sections.*.step_percentage.*' => 'required|numeric|min:0|max:100',
        ]);
        DB::beginTransaction();

        try {

            $filePath = null;
            if ($request->hasFile('deed_file')) {
                $filePath = $request->file('deed_file')->store('deeds', 'public');
            }

            $deed = Deed::create([
                'deed_name'     => $request->deed_name,
                'project_id'    => $request->project_id,
                'contractor_id' => $request->contractor_id,
                'deed_date'     => \Carbon\Carbon::createFromFormat(company()->date_format, $request->deed_date)->format('Y-m-d'),
                'deed_file'     => $filePath,
                'created_by'    => auth()->id(),
            ]);

            $grandTotal = 0;

            foreach ($request->sections as $section) {

                $perUnitRate = floatval($section['per_unit_rate']);
                $totalUnit   = floatval($section['total_unit']);
                $sectionAmount = $perUnitRate * $totalUnit;

                $detail = DeedDetail::create([
                    'deed_id'        => $deed->id,
                    'section_id'     => $section['section_id'],
                    'unit_type'      => $section['unit_type'],
                    'per_unit_rate'  => $perUnitRate,
                    'total_unit'     => $totalUnit,
                    'section_amount' => $sectionAmount,
                    'created_by'     => auth()->id(),
                ]);

                $grandTotal += $sectionAmount;

                $totalPercentage = 0;

                foreach ($section['steps'] as $index => $stepId) {
                    $percentage = floatval($section['step_percentage'][$index]);
                    $totalPercentage += $percentage;

                    DeedDetailsStep::create([
                        'deed_details_id' => $detail->id,
                        'deed_id'         => $deed->id,
                        'section_id'      => $section['section_id'],
                        'step_id'         => $stepId,
                        'budget_amount_percentage'=> $percentage,
                        'budget_amount'   => ($percentage / 100) * $sectionAmount,
                        'created_by'      => auth()->id(),
                    ]);
                }

                if (round($totalPercentage,2) != 100) {
                    throw new \Exception("Total step percentage must be 100% for each section.");
                }
            }

            $deed->update(['deed_total_amount' => $grandTotal]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => __('messages.recordSaved'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => __('messages.errorOccured'),
            ]);
        }
    }

    public function getSections($projectId)
    {
        $sections = Section::where('project_id', $projectId)->get();
        return response()->json(['sections' => $sections]);
    }

    public function getSteps($sectionId)
    {
        $steps = Step::where('section_id', $sectionId)->get();
        return response()->json(['steps' => $steps]);
    }

    public function edit($id)
    {
        $this->deed = Deed::with([
            'details.steps'
        ])->findOrFail($id);
        $this->projects = Project::all();
        $this->contractors = Contractor::with('type')->get();
        $this->projects = Project::all();
        $this->contractors = Contractor::all();
        $this->sections = Section::all();
        $this->steps = Step::all();

        $this->pageTitle = "Edit Deed";
        $this->view = 'deeds.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('deeds.create', $this->data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'deed_name'      => 'required|string|max:255',
            'project_id'     => 'required|exists:projects,id',
            'contractor_id'  => 'required|exists:contractors,id',
            'deed_date'      => 'required|date',
            'deed_file'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'sections'       => 'required|array|min:1',
            'sections.*.section_id'   => 'required|exists:sections,id',
            'sections.*.unit_type'    => 'required|string',
            'sections.*.per_unit_rate'=> 'required|numeric|min:0',
            'sections.*.total_unit'   => 'required|numeric|min:0',
            'sections.*.steps'         => 'required|array|min:1',
            'sections.*.steps.*'       => 'required|exists:steps,id',
            'sections.*.step_percentage' => 'required|array|min:1',
            'sections.*.step_percentage.*' => 'required|numeric|min:0|max:100',
        ]);

        $deed = Deed::findOrFail($id);
        DB::beginTransaction();

        try {
            // Handle file
            if ($request->hasFile('deed_file')) {
                if ($deed->deed_file) {
                    Storage::disk('public')->delete($deed->deed_file);
                }
                $deed->deed_file = $request->file('deed_file')->store('deeds', 'public');
            }

            // Update deed main info
            $deed->update([
                'deed_name'     => $request->deed_name,
                'project_id'    => $request->project_id,
                'contractor_id' => $request->contractor_id,
                'deed_date'     => \Carbon\Carbon::createFromFormat(company()->date_format, $request->deed_date)->format('Y-m-d'),
                'status'        => $request->status ?? $deed->status,
                'updated_by'    => auth()->id(),
            ]);

            // Delete old details
            DeedDetail::where('deed_id', $deed->id)->delete();
            DeedDetailsStep::where('deed_id', $deed->id)->delete();

            $grandTotal = 0;

            foreach ($request->sections as $section) {
                $perUnitRate = floatval($section['per_unit_rate']);
                $totalUnit   = floatval($section['total_unit']);
                $sectionAmount = $perUnitRate * $totalUnit;

                $detail = DeedDetail::create([
                    'deed_id'        => $deed->id,
                    'section_id'     => $section['section_id'],
                    'unit_type'      => $section['unit_type'],
                    'per_unit_rate'  => $perUnitRate,
                    'total_unit'     => $totalUnit,
                    'section_amount' => $sectionAmount,
                    'created_by'     => auth()->id(),
                ]);

                $grandTotal += $sectionAmount;

                $totalPercentage = 0;

                foreach ($section['steps'] as $index => $stepId) {
                    $percentage = floatval($section['step_percentage'][$index]);
                    $totalPercentage += $percentage;

                    DeedDetailsStep::create([
                        'deed_details_id' => $detail->id,
                        'deed_id'         => $deed->id,
                        'section_id'      => $section['section_id'],
                        'step_id'         => $stepId,
                        'budget_amount_percentage' => $percentage,
                        'budget_amount'   => ($percentage / 100) * $sectionAmount,
                        'created_by'      => auth()->id(),
                    ]);
                }

                if (round($totalPercentage, 2) != 100) {
                    throw new \Exception("Total step percentage must be 100% for section ID {$section['section_id']}");
                }
            }

            $deed->update(['deed_total_amount' => $grandTotal]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => __('messages.updateSuccess'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => __('messages.errorOccured'),
            ]);
        }
    }


    public function destroy($id)
    {
        Deed::destroy($id);

        return response()->json([
            'status'  => 'success',
            'message' => __('messages.deleteSuccess'),
        ]);
    }


    public function toggleStatus(Request $request)
    {
        $deed = Deed::findOrFail($request->id);
        $deed->status = $request->status;
        $deed->updated_by = user()->id;
        $deed->save();

        return response()->json([
            'status' => 'success',
            'message' => __('messages.updateSuccess')
        ]);
    }


    public function show($id)
    {
        $this->deed = Deed::with([
            'details.steps'
        ])->findOrFail($id);
        $this->projects = Project::all();
        $this->contractors = Contractor::with('type')->get();
        $this->projects = Project::all();
        $this->contractors = Contractor::all();
        $this->sections = Section::all();
        $this->steps = Step::all();

        $this->pageTitle = "View Deed";
        $this->view = 'deeds.ajax.show';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('deeds.create', $this->data);
    }
}
