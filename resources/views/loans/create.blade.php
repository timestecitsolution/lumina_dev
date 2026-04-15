@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Create Loan Request</h3>

    <form action="{{ route('loan.store') }}" method="POST">
        @csrf

        <select name="employee_id" id="employeeSelect" class="form-control select-picker" data-live-search="true" required >
            <option value="">-- Select Employee --</option>
            @foreach($employees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->user->name }} ({{ $employee->user->mobile ?? 'N/A' }})</option>
            @endforeach
        </select>

        <label>Loan Type</label>
        <select name="loan_type" class="form-control select-picker" data-live-search="true" required>
            <option value="">Select Investor</option>
            <option>Medical Loan</option>
            <option>Marriage Loan</option>
            <option>Salary Advance</option>
            <option>Development Advance</option>
        </select>


        <label>Available Loan Amount</label>
        <input type="number" name="" value="0.00" class="form-control loan_avail_amount" readonly>

        <label>Requested Amount</label>
        <input type="number" name="requested_amount" class="form-control">

        <label>Tenure (months)</label>
        <input type="number" name="tenure_months" class="form-control">

        

        <button class="btn btn-success mt-2">Submit</button>
    </form>
</div>
@endsection


@push('scripts')
<script>
    $(document).ready(function () {

        $('#employeeSelect').on('change', function () {

            let employeeId = $(this).val();

            if (employeeId === "") {
                return;
            }

            $.ajax({

                url: "{{ route('getEmployeeLoanInfo') }}",
                type: "GET",
                data: {
                    employeeId: employeeId
                },
                dataType: "json",

                success: function (response) {
                    console.log("AJAX Response:", response);
                },

                error: function (xhr) {
                    console.log("Error:", xhr.responseText);
                }
            });

        });

    });
</script>

@endpush
