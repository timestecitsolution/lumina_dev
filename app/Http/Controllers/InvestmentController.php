<?php

namespace App\Http\Controllers;

use App\DataTables\InvestmentDataTable;
use App\Models\Investment;
use App\Models\InvestmentTerm;
use App\Models\Investor;
use App\Models\BankTransaction;
use App\Models\Payment;
use App\Models\BankAccount;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvestmentController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Investments';
    }

    public function index(InvestmentDataTable $dataTable)
    {
        $this->pageTitle = 'All Investments';
        return $dataTable->render('investments.index', $this->data);
    }

    public function create()
    {
        $this->pageTitle = 'Add Investment';
        $this->investor = Investor::all();
        $this->banks = BankAccount::all();
        $this->projects = Project::all();
        return view('investments.create', $this->data);
    }

    public function store(Request $request)
    {
        $request->merge([
            'transaction_type' => strtolower($request->transaction_type),
        ]);
        $data = $request->validate([
            'date' => 'required|date',
            'investment_name' => 'required|string|max:255',
            'investor_id' => 'required|integer',
            'project_id' => 'nullable|integer',
            'amount' => 'required|numeric|min:0',
            'profit_percent' => 'nullable|numeric|min:0|max:10',
            'investment_type' => 'required|in:investment,loan',
            'provide_employee' => 'nullable|boolean',
            'transaction_type' => 'required|in:dr,cr',
            'bank_id' => 'required|integer',
            'note' => 'nullable|string',
            'timeline' => 'nullable|string',
            'refference' => 'nullable|string',
            'terms' => 'nullable|array',
            'terms.*' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($data, $request) {
            $investment = Investment::create($data);
            if (!empty($request->terms)) {
                foreach ($request->terms as $term) {
                    InvestmentTerm::create([
                        'investment_id' => $investment->id,
                        'term' => $term,
                    ]);
                }
            }
            $bank = BankAccount::findOrFail($data['bank_id']);

            if ($data['transaction_type'] == 'dr') {
                $bank_balance = $bank->bank_balance - $data['amount'];
                $title = "payment-debited";
                $type = "Dr";
            } else {
                $bank_balance = $bank->bank_balance + $data['amount'];
                $title = "payment-credited";
                $type = "Cr";
            }

            BankTransaction::create([
                'company_id' => 1,
                'bank_account_id' => $data['bank_id'],
                'amount' => $data['amount'],
                'transaction_relation' => $data['investment_type'],
                'type' => $type,
                'bank_balance' => round($bank_balance, 2),
                'title' => $title,
            ]);
            $bank->bank_balance = round($bank_balance, 2);
            $bank->save();
        });

        return redirect()
            ->route('investments.index')
            ->with('success', 'Investment created successfully.');
    }


    public function storeOld(Request $request)
    {
        
        $request->merge([
            'transaction_type' => strtolower($request->transaction_type),
        ]);
        
        $data = $request->validate([
            'date' => 'required|date',
            'investment_name' => 'required|string|max:255',
            'investor_id' => 'required',
            'project_id' => 'nullable|integer',
            'amount' => 'required|numeric|min:0',
            'profit_percent' => 'numeric|min:0|max:10',
            'investment_type' => 'required|in:investment,loan',
            'provide_employee' => 'nullable|boolean',
            'transaction_type' => 'required|in:dr,cr',
            'bank_id' => 'required|integer',
            'note' => 'nullable|string',
            'timeline' => 'nullable|string',
            'refference' => 'nullable|string',
        ]);
        
        DB::transaction(function () use ($data) {
            $investment = Investment::create($data);

            $bank_info = BankAccount::find($data['bank_id']);
            if($data['transaction_type'] == 'dr')
            {
                $bank_balance  = $bank_info->bank_balance - $data['amount'];
                $title  = "payment-debited";
                $transaction_type  = "Dr";
            }
            if($data['transaction_type'] == 'cr')
            {
                $bank_balance  = $bank_info->bank_balance + $data['amount'];
                $title  = "payment-credited";
                $transaction_type  = "Cr";
            }
            BankTransaction::create([
                'company_id' => 1,
                'bank_account_id' => $data['bank_id'] ?? null,
                'amount' => $data['amount'],
                'transaction_relation' => $data['investment_type'] ,
                'type' => $transaction_type ,
                'bank_balance' => round($bank_balance, 2),
                'title' => $title,
            ]);

            $bank_info->bank_balance = round($bank_balance, 2);
            $bank_info->save();
        });

        

        return redirect()->route('investments.index')->with('success', 'Investment created successfully.');
    }

    public function edit($id)
    {
        $this->pageTitle = 'Edit Investment';

        $this->investment = Investment::with('terms')->findOrFail($id);
        $this->investor = Investor::all();
        $this->banks = BankAccount::all();
        $this->projects = Project::all();

        return view('investments.edit', $this->data);
    }
    public function update(Request $request, $id)
    {
        $investment = Investment::findOrFail($id);
        // dd($request->all());
        $data = $request->validate([
            'date' => 'required|date',
            'investment_name' => 'required|string|max:255',
            'investor_id' => 'required|integer',
            'project_id' => 'nullable|integer',
            'amount' => 'required|numeric|min:0',
            'profit_percent' => 'nullable|numeric|min:0|max:10',
            'investment_type' => 'required|in:investment,loan',
            'provide_employee' => 'nullable|boolean',
            'transaction_type' => 'required|in:dr,cr',
            'bank_id' => 'required|integer',
            'note' => 'nullable|string',
            'timeline' => 'nullable|string',
            'refference' => 'nullable|string',
            'terms' => 'nullable|array',         
            'terms.*' => 'nullable|string|max:500', 
        ]);
       
        DB::transaction(function () use ($investment, $data, $request) {

            $bank = BankAccount::findOrFail($investment->bank_id);
            if ($investment->transaction_type == 'dr') {
                $bank->bank_balance += $investment->amount;
            } else {
                $bank->bank_balance -= $investment->amount;
            }

            if ($data['transaction_type'] == 'dr') {
                $bank->bank_balance -= $data['amount'];
                $title = "payment-debited";
                $type = "Dr";
            } else {
                $bank->bank_balance += $data['amount'];
                $title = "payment-credited";
                $type = "Cr";
            }

            $bank->bank_balance = round($bank->bank_balance, 2);
            $bank->save();
            $investment->update($data);
            InvestmentTerm::where('investment_id', $investment->id)->delete();

            if (!empty($request->terms)) {
                foreach ($request->terms as $term) {
                    if (!is_null($term) && trim($term) !== '') { 
                        InvestmentTerm::create([
                            'investment_id' => $investment->id,
                            'term' => $term,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('investments.index')->with('success', 'Investment updated successfully.');
    }


    public function show($id)
    {
        $this->investment = Investment::with(['investor', 'project', 'bank', 'terms'])->findOrFail($id);

        return view('investments.show', $this->data);
    }



    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $investment = Investment::with('terms', 'bankAccount')->findOrFail($id);

            $bank = $investment->bankAccount;

            if ($bank) {
                if ($investment->transaction_type == 'dr') {
                    $bank->bank_balance += $investment->amount;
                } else {
                    $bank->bank_balance -= $investment->amount;
                }
                $bank->bank_balance = round($bank->bank_balance, 2);
                $bank->save();
            }
            $investment->terms()->delete();
            $investment->delete();

            DB::commit();

            return redirect()->route('investments.index')
                ->with('success', 'Investment & related data deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

}
