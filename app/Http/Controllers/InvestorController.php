<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DataTables\InvestorDataTable;

class InvestorController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Investor';
    }

    public function index(InvestorDataTable $dataTable)
    {
        $this->pageTitle = "All Investors";

        return $dataTable->render('investors.index', $this->data);
    }

    public function create()
    {
        $this->pageTitle = 'Add Investor';

        return view('investors.create', $this->data);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'nullable|email|max:191|unique:investors,email',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:191',
            'address' => 'nullable|string',
            'assigned_employee_from_investor' => 'nullable|in:0,1',
            'notes' => 'nullable|string',
        ]);

        // ensure boolean value
        $data['assigned_employee_from_investor'] = $request->has('assigned_employee_from_investor') && $request->assigned_employee_from_investor == '1';

        Investor::create($data);

        return redirect()->route('investors.index')->with('success', 'Investor created successfully.');
    }

    public function show(Investor $investor)
    {
        $this->pageTitle = 'View Investor'; 
        return view('investors.show', $this->data);
    }

    public function edit(Investor $investor)
    {
        
        $this->investor = $investor;
        $this->pageTitle = 'Edit Investor'; 
        return view('investors.edit', $this->data);
    }

    public function update(Request $request, Investor $investor)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'nullable|email|max:191|unique:investors,email,' . $investor->id,
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:191',
            'address' => 'nullable|string',
            'assigned_employee_from_investor' => 'nullable|in:0,1',
            'notes' => 'nullable|string',
        ]);

        $data['assigned_employee_from_investor'] = $request->has('assigned_employee_from_investor') && $request->assigned_employee_from_investor == '1';

        $investor->update($data);

        return redirect()->route('investors.index')->with('success', 'Investor updated successfully.');
    }

    public function destroy(Investor $investor)
    {
        $investor->delete();
        return redirect()->route('investors.index')->with('success', 'Investor deleted.');
    }
}
