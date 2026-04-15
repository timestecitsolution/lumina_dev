<?php

namespace App\Http\Controllers;

use App\DataTables\StepDataTable;
use App\Models\Step;
use App\Models\Project;
use App\Models\Section;
use Illuminate\Http\Request;

class StepController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.step';
    }

    public function index(StepDataTable $dataTable)
    {
        $this->projects = Project::all();
        $this->sections = Section::all();
        return $dataTable->render('steps.index', $this->data);
    }

    public function create()
    {
        
        $this->pageTitle = "Add Step";
        $this->view = 'steps.create';
        $this->projects = Project::all();
        $this->sections = Section::all();

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('steps.ce', $this->data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required',
            'section_id' => 'required',
            'step_name' => 'required'
        ]);

        Step::create([
            'project_id' => $request->project_id,
            'section_id' => $request->section_id,
            'step_name' => $request->step_name,
            'step_description' => $request->step_description,
            'status' => $request->status,
            'created_by' => user()->id
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => __('messages.recordSaved'),
        ]);
    }

    public function edit($id)
    {

        $this->step = Step::findOrFail($id);
        $this->projects = Project::all();
        $this->sections = Section::all();


        $this->pageTitle = "Edit Step";
        $this->view = 'steps.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('steps.ce', $this->data);


    }

    public function update(Request $request, $id)
    {
        $step = Step::findOrFail($id);

        $step->update([
            'project_id' => $request->project_id,
            'section_id' => $request->section_id,
            'step_name' => $request->step_name,
            'step_description' => $request->step_description,
            'status' => $request->status,
            'updated_by' => user()->id
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => __('messages.updateSuccess'),
        ]);
    }

    public function destroy($id)
    {
        Step::destroy($id);
        return response()->json([
            'status'  => 'success',
            'message' => __('messages.deleteSuccess'),
        ]);
    }

    public function toggleStatus($id)
    {
        $step = Step::findOrFail($id);

        $step->status = ($step->status + 1) % 3;
        $step->updated_by = user()->id;
        $step->save();

        return response()->json(['status' => 'success']);
    }
}
