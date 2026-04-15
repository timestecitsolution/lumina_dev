@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Income Statement / Profit & Loss</h1>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Type</th>
                <th>Amount ({{ config('accounting_reports.currency') }})</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Revenue</td>
                <td>{{ number_format($income, 2) }}</td>
            </tr>
            <tr>
                <td>Expenses</td>
                <td>{{ number_format($expense, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Net Profit / Loss</strong></td>
                <td><strong>{{ number_format($net, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
