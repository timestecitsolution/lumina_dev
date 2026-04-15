<?php

namespace App\Http\Controllers;

use App\Enums\Salutation;
use App\Helper\Reply;
use App\Models\EmergencyContact;
use App\Models\User;
use App\Models\ValidationRole;
use App\Models\ValidationPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidationSettingController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.validationSettings';
        $this->activeSettingMenu = 'validation_settings';
    }

    public function index()
    {
        $tab = request('tab');

        
        $this->countries = countries();
        $this->salutations = Salutation::cases();

        switch ($tab) {

        case 'validation-settings':
            $this->validationRoles = ValidationRole::orderBy('id', 'desc')->get();
            $this->employees = User::whereHas('employeeDetail')
                                ->with(['employeeDetail.designation'])
                                ->get();
            $this->validationPermissions = ValidationPermission::with('validationRole')->groupBy('validation_role_id')->get();

            $role = null;
            $rolePermissions = collect();

            
            if (request()->has('validation_role_id')) {
                $roleId = request('validation_role_id');
               
                $role = ValidationRole::find($roleId);

                $rolePermissions = ValidationPermission::where('validation_role_id', $roleId)
                        ->with('employee.employeeDetail.designation')
                        ->orderBy('priority')
                        ->get();
            }

            $this->role = $role;
            $this->rolePermissions = $rolePermissions;
           

            $this->view = 'validation-settings.ajax.layer';
            break;
        
        default:
            $this->validationRoles = ValidationRole::orderBy('id', 'desc')->get();

            if (request()->has('id')) {
                $role = ValidationRole::find(request('id'));
            }

            $this->view = 'validation-settings.ajax.manage';
            break;
        }

        $this->activeTab = $tab ?: 'validation-manage';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle, 'activeTab' => $this->activeTab, 'role' => $role ?? null, 'rolePermissions' => $rolePermissions ?? null]);
        }

        return view('validation-settings.index', $this->data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'validation_name' => 'required|unique:validation_roles,validation_name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        ValidationRole::create([
            'validation_name' => $request->validation_name,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Validation role saved successfully'
        ]);
    }

    public function store_permission(Request $request)
    {
        
        $request->validate([
            'validation_role_id' => 'required|exists:validation_roles,id',
            'employees'          => 'required|array|min:1',
            'employees.*'        => 'required|exists:users,id',
            'designation_id'     => 'required|array|min:1',
            'designation_id.*'   => 'required|exists:designations,id',
            'priorities'         => 'required|array|min:1',
            'priorities.*'       => 'required|integer|min:1',
        ], [
            'validation_role_id.required' => 'Validation Name is required.',
            'employees.required'          => 'At least one employee is required.',
            'employees.*.required'        => 'Each employee must be selected.',
            'priorities.*.required'       => 'Priority is required for each employee.',
            'designation_id.*.required'   => 'Designation is required for each employee.',
        ]);

        
        ValidationPermission::where('validation_role_id', $request->validation_role_id)->delete();

        
        foreach ($request->employees as $index => $empId) {
            ValidationPermission::create([
                'validation_role_id' => $request->validation_role_id,
                'employee_id'        => $empId,
                'designation_id'     => $request->designation_id[$index] ?? null,
                'priority'           => $request->priorities[$index],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Validation permissions saved successfully.'
        ]);
    }


    public function update(Request $request, $id)
    {
        
        $request->validate([
            'validation_name' => 'required|unique:validation_roles,validation_name,' . $request->id
        ]);

        ValidationRole::where('id', $request->id)->update([
            'validation_name' => $request->validation_name
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Validation role updated successfully'
        ]);
    }

    public function update_permission(Request $request, $validationRoleId)
    {
        $request->validate([
            'validation_role_id' => 'required|exists:validation_roles,id',
            'employees'          => 'required|array|min:1',
            'employees.*'        => 'required|exists:users,id',
            'designation_id'     => 'required|array|min:1',
            'designation_id.*'   => 'required|exists:designations,id',
            'priorities'         => 'required|array|min:1',
            'priorities.*'       => 'required|integer|min:1',
        ], [
            'validation_role_id.required' => 'Validation Name is required.',
            'employees.required'          => 'At least one employee is required.',
            'employees.*.required'        => 'Each employee must be selected.',
            'priorities.*.required'       => 'Priority is required for each employee.',
            'designation_id.*.required'   => 'Designation is required for each employee.',
        ]);

        
        ValidationPermission::where('validation_role_id', $validationRoleId)->delete();

        
        foreach ($request->employees as $index => $empId) {
            ValidationPermission::create([
                'validation_role_id' => $request->validation_role_id,
                'employee_id'        => $empId,
                'designation_id'     => $request->designation_id[$index] ?? null,
                'priority'           => $request->priorities[$index],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Validation permissions updated successfully.'
        ]);
    }



    public function destroy($id)
    {
        
        $permissionExists = ValidationPermission::where('validation_role_id', $id)->exists();

        if ($permissionExists) {
            return response()->json([
                'status' => false,
                'message' => 'This validation role is already used in permissions. Delete not allowed.'
            ], 422);
        }

        $role = ValidationRole::findOrFail($id);
        $role->delete();

        return redirect()->back()->with('message', 'Validation role deleted successfully');
    }

    public function destroy_permission($id)
    {
        ValidationPermission::where('validation_role_id', $id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Validation permissions deleted successfully.'
        ]);
    }


}
