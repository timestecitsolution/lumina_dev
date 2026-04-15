@extends('layouts.app')

@section('content')
<h3>Edit Loan #{{ $loan->loan_no }} | {{ $loan->employee->name }}</h3>

<form action="{{ route('loan.update',$loan->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label>Requested Amount</label>
    <input type="number" name="requested_amount" value="{{ $loan->requested_amount }}" class="form-control">

    <label>Approved Amount</label>
    <input type="number" name="approved_amount" value="{{ $loan->approved_amount }}" class="form-control">

    <label>Interest Rate (%)</label>
    <input type="number" step="0.01" name="interest_rate" value="{{ $loan->interest_rate }}" class="form-control">

    <label>Tenure (months)</label>
    <input type="number" name="tenure_months" value="{{ $loan->tenure_months }}" class="form-control">

    <label>Disbursement Date</label>
    <input type="date" name="disbursement_date" value="{{ $loan->disbursement_date ? $loan->disbursement_date->format('Y-m-d') : '' }}" class="form-control">

    <label>Start Deduction Date</label>
    <input type="date" name="start_deduction_date" value="{{ $loan->start_deduction_date ? $loan->start_deduction_date->format('Y-m-d') : '' }}" class="form-control">

    <button class="btn btn-primary mt-2">Update Loan</button>
</form>
@endsection
