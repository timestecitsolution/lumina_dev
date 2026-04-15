@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Investor</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('investors.update', $investor) }}" method="POST">
        @method('PUT')
        @include('investors._form')
    </form>
</div>
@endsection
