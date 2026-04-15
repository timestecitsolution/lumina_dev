@extends('layouts.app')

@section('content')

<h3>Loan #{{ $loan->loan_no }} | {{ $loan->employee->name }}</h3>

<p>Requested: {{ $loan->requested_amount }}</p>
<p>Approved: {{ $loan->approved_amount }}</p>
<p>Status: {{ $loan->status }}</p>
<p>Outstanding: {{ $loan->outstanding }}</p>

<hr>

@if($loan->status == 'requested')
<h4>Approve Loan</h4>
<form action="{{ route('loan.approve',$loan->id) }}" method="POST">
    @csrf
    <label>Approved Amount</label>
    <input name="approved_amount" class="form-control">

    <label>Interest (%)</label>
    <input name="interest_rate" class="form-control">

    <label>Disbursement Date</label>
    <input type="date" name="disbursement_date" class="form-control">

    <label>Start Deduction Date</label>
    <input type="date" name="start_deduction_date" class="form-control">

    <label>Tenure Months</label>
    <input name="tenure_months" class="form-control">

    <button class="btn btn-primary mt-2">Approve</button>
</form>
@endif

<hr>

<h4>Schedule</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Due Date</th>
            <th>Due Amount</th>
            <th>Principal</th>
            <th>Interest</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($loan->schedules as $s)
        <tr>
            <td>{{ $s->due_date }}</td>
            <td>{{ $s->due_amount }}</td>
            <td>{{ $s->principal_component }}</td>
            <td>{{ $s->interest_component }}</td>
            <td>{{ $s->status }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<hr>

<h4>Add Payment</h4>
<form action="{{ route('loan.pay',$loan->id) }}" method="POST">
    @csrf
    <label>Amount</label>
    <input name="amount" class="form-control">

    <label>Payment Date</label>
    <input type="date" name="payment_date" class="form-control">

    <label>Method</label>
    <select name="method" class="form-control">
        <option value="manual_cash">Manual Cash</option>
        <option value="bank_transfer">Bank Transfer</option>
        <option value="salary_deduction">Salary Deduction</option>
    </select>

    <label>Reference</label>
    <input name="reference" class="form-control">

    <button class="btn btn-success mt-2">Submit</button>
</form>

@endsection
