@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Balance Sheet</h1>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Category</th>
                <th>Amount ({{ config('accounting_reports.currency') }})</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Assets</td>
                <td>{{ number_format($assets, 2) }}</td>
            </tr>
            <tr>
                <td>Liabilities</td>
                <td>{{ number_format($liabilities, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Equity</strong></td>
                <td><strong>{{ number_format($equity, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
