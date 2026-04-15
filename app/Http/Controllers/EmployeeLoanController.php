<?php

namespace App\Http\Controllers;


use App\DataTables\EmployeeLoanDataTable;
use App\Models\EmployeeDetails;
use App\Models\EmployeeLoan;
use App\Models\EmployeeLoanPayment;
use App\Models\EmployeeLoanSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EmployeeLoanController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Loans';
    }


    public function index(EmployeeLoanDataTable $dataTable)
    {
        $this->pageTitle = 'All Loan';
        return $dataTable->render('loans.index', $this->data);
    }

    public function create()
    {
    	$this->employees = EmployeeDetails::whereHas('user', function($query){
						        $query->where('status', 'active'); 
						    })->get();
        return view('loans.create', $this->data);
    }

    // Store Loan Request
    public function store(Request $req)
    {
        $data = $req->validate([
            'employee_id'=>'required',
            'requested_amount'=>'required|numeric',
            'tenure_months'=>'required|integer',
            'repayment_type'=>'required'
        ]);

        $loan = EmployeeLoan::create(array_merge($data, [
            'loan_no' => 'LN'.time(),
            'status' => 'requested'
        ]));

        return redirect()->route('loan.show', $loan->id);
    }

    // Show Loan
    public function show($id)
    {
        $loan = EmployeeLoan::with(['employee','payments','schedules'])->findOrFail($id);
        return view('loans.show', compact('loan'));
    }

    // Approve Loan
    public function approve(Request $req, $id)
    {
        $req->validate([
            'approved_amount'=>'required|numeric',
            'interest_rate'=>'required|numeric',
            'disbursement_date'=>'required|date',
            'start_deduction_date'=>'required|date',
            'tenure_months'=>'required'
        ]);

        $loan = EmployeeLoan::findOrFail($id);

        $loan->update([
            'approved_amount'=>$req->approved_amount,
            'interest_rate'=>$req->interest_rate,
            'disbursement_date'=>$req->disbursement_date,
            'start_deduction_date'=>$req->start_deduction_date,
            'tenure_months'=>$req->tenure_months,
            'status'=>'approved',
            'approved_by'=>auth()->id(),
            'approved_at'=>now()
        ]);

        $this->generateSchedule($loan);

        return back()->with('success','Loan approved & schedule generated.');
    }

    // EMI Schedule Generator
    private function generateSchedule(EmployeeLoan $loan)
    {
        $P = $loan->approved_amount;
        $r = ($loan->interest_rate / 100) / 12;
        $n = $loan->tenure_months;

        $factor = pow(1+$r, $n);
        $emi = $P * $r * $factor / ($factor - 1);

        $balance = $P;
        $start = Carbon::parse($loan->start_deduction_date);

        for ($i=1; $i <= $n; $i++) {

            $interest = $balance * $r;
            $principal = $emi - $interest;

            if ($i == $n) {
                $principal = $balance;
                $emi = $principal + $interest;
            }

            EmployeeLoanSchedule::create([
                'loan_id'=>$loan->id,
                'due_date'=>$start->copy()->addMonths($i-1),
                'due_amount'=>round($emi,2),
                'principal_component'=>round($principal,2),
                'interest_component'=>round($interest,2),
            ]);

            $balance -= $principal;
        }
    }

    // Manual Payment
    public function pay(Request $req, $id)
    {
        $req->validate([
            'amount'=>'required|numeric',
            'payment_date'=>'required|date',
            'method'=>'required'
        ]);

        EmployeeLoanPayment::create([
            'loan_id'=>$id,
            'amount'=>$req->amount,
            'payment_date'=>$req->payment_date,
            'method'=>$req->method,
            'reference'=>$req->reference,
            'note'=>$req->note
        ]);

        return back()->with('success','Payment added successfully');
    }


	public function edit($id)
	{
	    $loan = EmployeeLoan::findOrFail($id);
	    return view('loans.edit', compact('loan'));
	}


	public function update(Request $req, $id)
	{
	    $loan = EmployeeLoan::findOrFail($id);


	    if(!in_array($loan->status,['requested','approved'])){
	        return back()->with('error','Cannot edit this loan at this stage.');
	    }

	    $req->validate([
	        'requested_amount'=>'required|numeric',
	        'approved_amount'=>'nullable|numeric',
	        'interest_rate'=>'nullable|numeric',
	        'tenure_months'=>'required|integer',
	        'disbursement_date'=>'nullable|date',
	        'start_deduction_date'=>'nullable|date',
	    ]);

	    $loan->update([
	        'requested_amount'=>$req->requested_amount,
	        'approved_amount'=>$req->approved_amount ?? $loan->approved_amount,
	        'interest_rate'=>$req->interest_rate ?? $loan->interest_rate,
	        'tenure_months'=>$req->tenure_months,
	        'disbursement_date'=>$req->disbursement_date ?? $loan->disbursement_date,
	        'start_deduction_date'=>$req->start_deduction_date ?? $loan->start_deduction_date,
	    ]);


	    if($loan->status == 'approved'){
	        $loan->schedules()->delete();
	        $this->generateSchedule($loan);
	    }

	    return redirect()->route('loan.show',$loan->id)->with('success','Loan updated successfully');
	}


	public function getEmployeeLoanInfo(Request $request)
	{
		
	    $employee = EmployeeDetails::with('user')->find($request->employeeId);

	    $employee_loan = EmployeeLoan::where('paid_status', 0)
						    ->where('employee_id', $request->employeeId)
						    ->sum('approved_amount');


	    if (!$employee) {
	        return response()->json([
	            'status' => false,
	            'message' => 'Employee not found'
	        ], 404);
	    }

	    return response()->json([
	        'status'        => true,
	        'id'            => $employee->id,
	        'name'          => $employee->user->name ?? 'N/A',
	        'mobile'        => $employee->user->mobile ?? 'N/A',
	        'designation'   => $employee->designation ?? 'N/A',
	        'department'    => $employee->department ?? 'N/A',
	        'employee_loan' => $employee_loan ?? 0,
	    ]);
	}


}
