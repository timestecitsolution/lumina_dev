<?php
namespace App\Http\Controllers;

use App\Helper\Reply;
use App\DataTables\SectionDataTable;
use App\Models\Section;
use App\Models\Project;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;


class SectionController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.section';
    }


    public function index(SectionDataTable $dataTable)
    {
        $this->projects = Project::all();
        return $dataTable->render('sections.crud.index', $this->data);
    }

    public function create()
    {
        $this->pageTitle = "Add Section";
        $this->view = 'sections.crud.create';
        $this->projects = Project::all();

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('sections.crud.ce', $this->data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required',
            'section_name' => 'required|string|max:255',
        ]);

        Section::create([
            'project_id' => $request->project_id,
            'section_name' => $request->section_name,
            'section_description' => $request->section_description,
            'status' => $request->status,
            'created_by' => user()->id,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => __('messages.recordSaved'),
        ]);
    }


    public function edit($id)
    {
        $this->section = Section::findOrFail($id);
        $this->projects = Project::all();
        $this->pageTitle = "Edit Section";
        $this->view = 'sections.crud.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('sections.crud.ce', $this->data);

    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'project_id' => 'required',
            'section_name' => 'required'
        ]);

        $section = Section::findOrFail($id);

        $section->update([
            'project_id' => $request->project_id,
            'section_name' => $request->section_name,
            'section_description' => $request->section_description,
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
        Section::destroy($id);
        

        return response()->json([
            'status'  => 'success',
            'message' => __('messages.deleteSuccess'),
        ]);
    }

    public function toggleStatus(Request $request)
    {
        $section = Section::findOrFail($request->id);
        $section->status = $request->status;
        $section->updated_by = user()->id;
        $section->save();

        return response()->json([
            'status' => 'success',
            'message' => __('messages.updateSuccess')
        ]);
    }
}
