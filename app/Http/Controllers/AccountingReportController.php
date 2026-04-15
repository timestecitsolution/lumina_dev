<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Purchase\Entities\PurchaseSetting;
use Carbon\Carbon;


class AccountingReportController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Report';

        $this->middleware(function ($request, $next) {
            return $next($request);
        });
    }

    public function ledger(Request $request)
    {
        $this->pageTitle = 'Ledger Report';
        $from = $request->from;
        $to = $request->to;
        $this->date_range = $request->date_range;
        
        if($request->has('date_range') && !empty($request->date_range)){
           
            $dates = explode(' To ', $request->date_range);

            if(count($dates) == 2){
                $from = Carbon::createFromFormat('d-m-Y', $dates[0])->format('Y-m-d');
                $to   = Carbon::createFromFormat('d-m-Y', $dates[1])->format('Y-m-d');
            }
        }
       
        $ledger_entries = collect();

        $bank_transactions = DB::table('bank_transactions')
            ->when($from, fn($q) => $q->whereDate('transaction_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('transaction_date', '<=', $to))
            ->get();

        $ledger_entries = $ledger_entries->merge($bank_transactions->map(fn($t)=>[
            'date' => $t->transaction_date,
            'account' => 'Bank: '.$t->bank_account_id,
            'description' => $t->notes ?? '',
            'debit' => $t->type === 'Dr' ? $t->amount : 0,
            'credit' => $t->type === 'Cr' ? $t->amount : 0,
        ]));

        $invoices = DB::table('invoices')
            ->where('status','paid')
            ->when($from, fn($q) => $q->whereDate('issue_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('issue_date', '<=', $to))
            ->get();

        $ledger_entries = $ledger_entries->merge($invoices->map(fn($i)=>[
            'date' => $i->issue_date,
            'account' => 'Customer: '.$i->client_id,
            'description' => 'Invoice #'.$i->id,
            'debit' => 0,
            'credit' => $i->total,
        ]));

        $purchase_payments = DB::table('payments')
            ->when($from, fn($q) => $q->whereDate('paid_on', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('paid_on', '<=', $to))
            ->get();

        $ledger_entries = $ledger_entries->merge($purchase_payments->map(fn($p)=>[
            'date' => $p->paid_on,
            'account' => 'Vendor: '.$p->account_id,
            'description' => 'Payment #'.$p->id,
            'debit' => $p->amount,
            'credit' => 0,
        ]));

        $expenses = DB::table('expenses')
            ->when($from, fn($q) => $q->whereDate('purchase_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('purchase_date', '<=', $to))
            ->get();

        $ledger_entries = $ledger_entries->merge($expenses->map(fn($e)=>[
            'date' => $e->purchase_date,
            'account' => 'Expense: '.$e->category_id,
            'description' => $e->notes ?? '',
            'debit' => $e->price,
            'credit' => 0,
        ]));


        $this->ledger_entries = $ledger_entries->sortByDesc('date')->values();

        return view('accounting_reports.ledger', $this->data);
    }

    public function trialBalance(Request $request)
    {
        $debit = DB::table('expenses')->sum('amount');
        $credit = DB::table('payments')->sum('amount');

        return view('accounting_reports.trial_balance', compact('debit','credit'));
    }

    public function incomeStatement(Request $request)
    {
        $from = $request->from;
        $to = $request->to;

        $income = DB::table('invoices')
            ->where('status','paid')
            ->when($from, fn($q) => $q->whereDate('issue_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('issue_date', '<=', $to))
            ->sum('total');

        $expense = DB::table('expenses')
            ->when($from, fn($q) => $q->whereDate('expense_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('expense_date', '<=', $to))
            ->sum('amount');

        $net = $income - $expense;

        return view('accounting_reports.income_statement', compact('income','expense','net'));
    }

    public function balanceSheet()
    {
        $assets = DB::table('bank_accounts')->sum('balance');
        $liabilities = DB::table('expenses')->sum('amount'); // সহজ উদাহরণ
        $equity = $assets - $liabilities;

        return view('accounting_reports.balance_sheet', compact('assets','liabilities','equity'));
    }

    public function cashBook(Request $request)
    {
        $from = $request->from;
        $to = $request->to;

        $transactions = DB::table('bank_transactions')
            ->when($from, fn($q) => $q->whereDate('transaction_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('transaction_date', '<=', $to))
            ->orderBy('transaction_date')
            ->get();

        return view('accounting_reports.cash_book', compact('transactions'));
    }
}
