<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contractor;
use App\Models\ContractorType;
use App\DataTables\ContractorDataTable;
use Illuminate\Support\Facades\Storage;

class ContractorController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.contractor';
    }


    public function index(ContractorDataTable $dataTable)
    {
        $this->types = ContractorType::where('status',1)->get();
        return $dataTable->render('contractors.index', $this->data);
    }

    public function create()
    {

        $this->pageTitle = "Add Contrtactor";
        $this->view = 'contractors.ajax.create';
        $this->types = ContractorType::where('status',1)->get();
        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('contractors.create', $this->data);

    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contractor_type_id' => 'required|exists:contractor_types,id',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'nullable|string',
            'tin' => 'nullable|string|max:50',
            'bin' => 'nullable|string|max:50',
            'trade_license_no' => 'nullable|string|max:100',
            'trade_license_img' => 'nullable|file|image|max:2048',
            'nid' => 'nullable|string|max:50',
            'nid_img' => 'nullable|file|image|max:2048',
            'profile_img' => 'nullable|file|image|max:2048',
            'status' => 'required|in:1,0'
        ]);

        // Handle files
        foreach(['trade_license_img','nid_img','profile_img'] as $fileField){
            if($request->hasFile($fileField)){
                $data[$fileField] = $request->file($fileField)->store('contractors','public');
            }
        }

        $data['created_by'] = auth()->id();

        Contractor::create($data);

        return response()->json([
            'status'  => 'success',
            'message' => __('messages.recordSaved'),
        ]);
    }

    public function show($id)
    {

        $this->pageTitle = "View Contrtactor";
        $this->view = 'contractors.show';
        $this->contractor = Contractor::findOrFail($id);
        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('contractors.create', $this->data);
    }

    public function edit($id)
    {

        $this->contractor = Contractor::findOrFail($id);
        $this->types = ContractorType::where('status',1)->get();
        $this->pageTitle = "Edit Contractor";
        $this->view = 'contractors.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('contractors.create', $this->data);

    }

    public function update(Request $request, Contractor $contractor)
    {
        $data = $request->validate([
            'contractor_type_id' => 'required|exists:contractor_types,id',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'nullable|string',
            'tin' => 'nullable|string|max:50',
            'bin' => 'nullable|string|max:50',
            'trade_license_no' => 'nullable|string|max:100',
            'trade_license_img' => 'nullable|file|image|max:2048',
            'nid' => 'nullable|string|max:50',
            'nid_img' => 'nullable|file|image|max:2048',
            'profile_img' => 'nullable|file|image|max:2048',
            'status' => 'required|in:1,0'
        ]);

        foreach(['trade_license_img','nid_img','profile_img'] as $fileField){
            if($request->hasFile($fileField)){
                // delete old
                if($contractor->$fileField){
                    Storage::disk('public')->delete($contractor->$fileField);
                }
                $data[$fileField] = $request->file($fileField)->store('contractors','public');
            }
        }

        $data['updated_by'] = auth()->id();

        $contractor->update($data);

        return response()->json([
            'status'  => 'success',
            'message' => __('messages.updateSuccess'),
        ]);
    }

    public function destroy($id)
    {
        Contractor::destroy($id);
        

        return response()->json([
            'status'  => 'success',
            'message' => __('messages.deleteSuccess'),
        ]);
    }

    
    public function toggleStatus(Request $request)
    {
        $contractor = Contractor::findOrFail($request->id);
        $contractor->status = $request->status; // 0 or 1
        $contractor->updated_by = auth()->id();
        $contractor->save();

        return response()->json([
            'status' => 'success',
            'message' => __('messages.updateSuccess')
        ]);
    }
}
