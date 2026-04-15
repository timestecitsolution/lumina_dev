@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Cash / Bank Book</h1>
    <form method="GET" action="{{ route('accounting-reports.cash-book') }}">
        <input type="date" name="from" value="{{ request('from') }}">
        <input type="date" name="to" value="{{ request('to') }}">
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Date</th>
                <th>Bank / Cash Account</th>
                <th>Description</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            @php $balance = 0; @endphp
            @foreach($transactions as $t)
                @php
                    $balance += ($t->debit ?? 0) - ($t->credit ?? 0);
                @endphp
                <tr>
                    <td>{{ $t->transaction_date }}</td>
                    <td>{{ $t->bank_account_name ?? 'N/A' }}</td>
                    <td>{{ $t->description }}</td>
                    <td>{{ $t->debit ?? 0 }}</td>
                    <td>{{ $t->credit ?? 0 }}</td>
                    <td>{{ number_format($balance, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
