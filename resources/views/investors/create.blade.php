@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Investor</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('investors.store') }}" method="POST">
        @include('investors._form')
    </form>
</div>
@endsection
