<?php

namespace App\Http\Controllers;

use App\Models\ContractorType;
use Illuminate\Http\Request;
use App\DataTables\ContractorTypeDataTable;

class ContractorTypeController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.contractor_type';
    }

    public function index(ContractorTypeDataTable $dataTable)
    {
        return $dataTable->render('contractor-types.index', $this->data);
    }

    public function create()
    {

        $this->pageTitle = "Add Contractor Types";
        $this->view = 'contractor-types.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('contractor-types.create', $this->data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type_name' => 'required|string|max:255',
            'status' => 'required|in:yes,no',
        ]);

        ContractorType::create([
            'type_name' => $request->type_name,
            'description' => $request->description,
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

        $this->contractorType = ContractorType::findOrFail($id);
        $this->pageTitle = "Edit Contractor Type";
        $this->view = 'contractor-types.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('contractor-types.create', $this->data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type_name' => 'required|string|max:255',
            'status' => 'required|in:yes,no',
        ]);

        $contractorType = ContractorType::findOrFail($id);

        $contractorType->update([
            'type_name' => $request->type_name,
            'description' => $request->description,
            'status' => $request->status,
            'updated_by' => user()->id,
        ]);


        return response()->json([
            'status'  => 'success',
            'message' => __('messages.updateSuccess'),
        ]);
    }

    public function destroy($id)
    {
        ContractorType::destroy($id);
        return response()->json([
            'status'  => 'success',
            'message' => __('messages.deleteSuccess'),
        ]);
    }


    public function toggleStatus(Request $request)
    {
        $contractorType = ContractorType::findOrFail($request->id);
        $contractorType->status = $request->status;
        $contractorType->updated_by = user()->id;
        $contractorType->save();

        return response()->json([
            'status' => 'success',
            'message' => __('messages.updateSuccess')
        ]);
    }
}
