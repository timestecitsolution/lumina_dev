@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Investor Details</h1>

    <div class="card">
        <div class="card-body">
            <h4>{{ $investor->name }}</h4>
            <p><strong>Company:</strong> {{ $investor->company }}</p>
            <p><strong>Email:</strong> {{ $investor->email }}</p>
            <p><strong>Phone:</strong> {{ $investor->phone }}</p>
            <p><strong>Address:</strong><br> {!! nl2br(e($investor->address)) !!}</p>
            <p><strong>Assigned Employee from Investor:</strong> {{ $investor->assigned_employee_from_investor ? 'Yes' : 'No' }}</p>
            <p><strong>Notes:</strong><br> {!! nl2br(e($investor->notes)) !!}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('investors.edit', $investor) }}" class="btn btn-primary">Edit</a>
        <a href="{{ route('investors.index') }}" class="btn btn-secondary">Back to list</a>
    </div>
</div>
@endsection
