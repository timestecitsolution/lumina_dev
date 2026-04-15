@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Trial Balance</h1>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Type</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Debit (Expenses)</td>
                <td>{{ number_format($debit, 2) }}</td>
            </tr>
            <tr>
                <td>Credit (Payments)</td>
                <td>{{ number_format($credit, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Net</strong></td>
                <td><strong>{{ number_format($credit - $debit, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
