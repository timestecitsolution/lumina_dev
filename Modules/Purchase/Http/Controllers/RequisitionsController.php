<?php

namespace Modules\Purchase\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tax;
use App\Helper\Reply;
use App\Models\Currency;
use App\Models\UnitType;
use App\Models\Project;
use App\Models\ValidationRole;
use App\Models\ValidationPermission;
use Illuminate\Http\Request;
use App\Models\CompanyAddress;
use Illuminate\Support\Facades\App;
use App\Models\PaymentGatewayCredentials;
use Modules\Purchase\Entities\PurchaseBill;
use Modules\Purchase\Entities\PurchaseItem;
use Illuminate\Contracts\Support\Renderable;
use Modules\Purchase\Entities\PurchaseOrder;
use Modules\Purchase\Entities\PurchaseVendor;
use Modules\Purchase\Http\Requests\StoreBill;

use Modules\Purchase\Entities\Requisition;
use Modules\Purchase\Entities\RequisitionItem;
use Modules\Purchase\Entities\RequisitionApproval;


use Modules\Purchase\Entities\PurchaseSetting;
use App\Http\Controllers\AccountBaseController;
use Modules\Purchase\Events\NewPurchaseBillEvent;
use Modules\Purchase\Entities\PurchaseBillHistory;
use Modules\Purchase\Entities\PurchasePaymentHistory;
use Modules\Purchase\DataTables\PurchaseBillDataTable;
use Modules\Purchase\DataTables\RequisitionDataTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;


class RequisitionsController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'purchase::app.menu.requisition';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function index(RequisitionDataTable $dataTable)
    {
       
        // $this->viewBillPermission = user()->permission('view_bill');
        // $this->addBillPermission = user()->permission('add_bill');
        // $this->vendors = PurchaseVendor::with('currency')->get();

        // abort_403(!in_array($this->viewBillPermission, ['all', 'added', 'owned', 'both']));
        $this->pageTitle = 'purchase::app.menu.requisition';

        return $dataTable->render('purchase::requisition.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $this->pageTitle = __('purchase::app.requisition.create_requisistion');
        $this->vendor = PurchaseBill::lastPurchaseBillNumber() + 1;
        $this->projects = Project::all();
       
        if (request()->ajax()) {
            $html = view('purchase::requisition.ajax.create', $this->data)->render();
            
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'purchase::requisition.ajax.create';

        return view('purchase::requisition.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request  $request)
    {

        $validator = Validator::make($request->all(), [
            'req_no'     => 'required|unique:requisitions,req_no',
            'delivery_place' => 'required',
            'project_id' => 'required'
        ], [
            'req_no.required'     => 'Requisition No required',
            'req_no.unique'       => 'Requisition No already exists',
            'delivery_place'       => 'Please Enter Delivery Place',
            'project_id.required' => 'Please Select Project'
        ]);

        if ($validator->fails()) {
            return reply::error($validator->errors()->first());
        }


        DB::transaction(function () use ($request) {
            $requisition = Requisition::create([
                'project_id'    => $request->project_id,
                'requested_by'   => Auth::id(),
                'req_no'         => $request->req_no ?? 'REQ-' . time(),
                 'req_date'       => now()->toDateString(),
                'delivery_date'  => $request->delivery_date,
                'delivery_place' => $request->delivery_place,
                'note'           => $request->note,
                'status'         => 'pending',
            ]);
            foreach ($request->items as $item) {
                RequisitionItem::create([
                    'requisition_id' => $requisition->id,
                    'item_name'      => $item['item_name'],
                    'quantity'       => $item['quantity'],
                    'unit'           => $item['unit'] ?? null,
                    'position'       => $item['position'] ?? 0,
                ]);
            }
        });
        return reply::success(__('messages.recordSaved'));
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $this->pageTitle = __('purchase::app.requisition.view_requisition');
        $this->requisition = Requisition::with(['items', 'project'])->findOrFail($id);

        // সব approvals (already done)
        $this->requisition_approvals = RequisitionApproval::with('actionBy')->where('requisition_id', $id)
            ->get()
            ->keyBy('employee_id'); // employee_id => approval

        // validation permissions with priority
        $this->validation_permissions = ValidationPermission::with(['employee','designation'])
            ->orderBy('priority', 'asc')
            ->where('validation_role_id', 1) 
            ->get();
        $this->user = user(); // current logged in user
        

        $approvedEmployeeIds = RequisitionApproval::where('requisition_id', $id)
        ->where('action', 'approved')
        ->pluck('employee_id')
        ->toArray();

        $this->nextApproval = ValidationPermission::with(['employee','designation'])->whereNotIn('employee_id', $approvedEmployeeIds)
            ->where('validation_role_id', 1) 
            ->orderBy('priority', 'asc')
            ->first();
       
        return view('purchase::requisition.ajax.show', $this->data);
    }



    public function action(Request $request, $id)
    {

        $requisition = Requisition::findOrFail($id);

        $user = user();

        // get next approval
        $validation_permissions = ValidationPermission::where('validation_role_id', 1) // adjust role
            ->orderBy('priority', 'asc')->get();

        $approvedEmployeeIds = RequisitionApproval::where('requisition_id', $id)
        ->where('action', 'approved')
        ->pluck('employee_id')
        ->toArray();

        $this->nextApproval = ValidationPermission::with(['employee','designation'])->whereNotIn('employee_id', $approvedEmployeeIds)
            ->where('validation_role_id', 1) 
            ->orderBy('priority', 'asc')
            ->first();
         
        $isSuperAdmin = ($user->id == 1);
        
        if ($isSuperAdmin || ($this->nextApproval && $this->nextApproval->employee_id == $user->id)) {
           
            RequisitionApproval::create([
                'requisition_id'    => $requisition->id,
                'action_by'         => $request->action_by,
                'employee_id'       => $request->employee_id,
                'action_role'       => $request->action_role ?? null,
                'action'            => $request->action, 
                'comment'           => $request->comment,
            ]);

            
            if($request->action == 'rejected'){
                RequisitionApproval::where('requisition_id', $requisition->id)->delete();
                $requisition->update(['status'=>'rejected']);
                return back()->with('success', 'Action performed successfully');
            } else {
                // check if all approvals done
                $remaining = $validation_permissions->filter(function($perm) use ($requisition) {
                    return !RequisitionApproval::where('requisition_id', $requisition->id)
                        ->where('employee_id', $perm->employee_id)->exists();
                });

                if($remaining->isEmpty()){
                    $requisition->update(['status'=>'approved']);
                }
            }

            return back()->with('success', 'Action performed successfully');
        }

        abort(403, 'You cannot approve this requisition yet.');
    }




    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $this->requisition = Requisition::with('items')->findOrFail($id);
        $this->projects = Project::all();
        $this->pageTitle = 'Edit Requisition';

        if (request()->ajax()) {
            $html = view('purchase::requisition.ajax.edit', $this->data)->render();
            
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'purchase::requisition.ajax.edit';

        return view('purchase::requisition.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'delivery_place' => 'required',
            'project_id' => 'required'
        ]);

        DB::transaction(function () use ($request, $id) {

            $requisition = Requisition::findOrFail($id);

            $requisition->update([
                'project_id' => $request->project_id,
                'delivery_date' => $request->delivery_date,
                'delivery_place' => $request->delivery_place,
                'note' => $request->note,
                'status' => 'pending',
            ]);

            RequisitionItem::where('requisition_id', $id)->delete();

            foreach ($request->items as $item) {
                RequisitionItem::create([
                    'requisition_id' => $id,
                    'item_name' => $item['item_name'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? null,
                    'position' => $item['position'] ?? 0,
                ]);
            }
        });

        return Reply::success('Requisition Updated Successfully');
    }


    public function destroy($id)
    {
        
    }

}
