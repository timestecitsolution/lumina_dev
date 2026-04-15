@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Investment Details</h3>
    <div class="card mb-3">
        <div class="card-body">
            <p><strong>ID:</strong> {{ $investment->id }}</p>
            <p><strong>Date:</strong> {{ $investment->date }}</p>
            <p><strong>Investment Name:</strong> {{ $investment->investment_name }}</p>
            <p><strong>Investor:</strong> {{ $investment->investor->name ?? 'N/A' }}</p>
            <p><strong>Project:</strong> {{ $investment->project->project_name ?? 'N/A' }}</p>
            <p><strong>Amount:</strong> {{ number_format($investment->amount, 2) }}</p>
            <p><strong>Profit Percent:</strong> {{ $investment->profit_percent ?? '-' }}%</p>
            <p><strong>Investment Type:</strong> {{ ucfirst($investment->investment_type) }}</p>
            <p><strong>Provide Employee:</strong> {{ $investment->provide_employee ? 'Yes' : 'No' }}</p>
            <p><strong>Timeline:</strong> {{ $investment->timeline ?? '-' }}</p>
            <p><strong>Reference:</strong> {{ $investment->refference ?? '-' }}</p>
            <p><strong>Transaction Type:</strong> {{ strtoupper($investment->transaction_type) }}</p>
            <p><strong>Bank:</strong> {{ $investment->bank->bank_name ?? '-' }} (Balance: {{ number_format($investment->bank->bank_balance,2) }})</p>
            <p><strong>Note:</strong> {{ $investment->note ?? '-' }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Terms & Conditions</h5>
        </div>
        <div class="card-body">
            @if($investment->terms->count())
                <ul class="list-group">
                    @foreach($investment->terms as $term)
                        <li class="list-group-item">{{ $term->term }}</li>
                    @endforeach
                </ul>
            @else
                <p>No terms added.</p>
            @endif
        </div>
    </div>

    <a href="{{ route('investments.index') }}" class="btn btn-secondary mt-3 mb-4">Back to Investments</a>
</div>
@endsection
