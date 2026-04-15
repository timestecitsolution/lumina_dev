@extends('layouts.app')
@section('content')

<div class="container">
    <h3>Add Investment</h3>
    <form action="{{ route('investments.store') }}" method="POST">
        @csrf

        <!-- Date -->
        <div class="form-group mb-2">
            <label>Date</label>
            <input type="date" name="date" class="form-control" required>
        </div>

        <!-- Investment Name -->
        <div class="form-group mb-2">
            <label>Investment Name</label>
            <input type="text" name="investment_name" class="form-control" required>
        </div>

        <!-- Investor -->
        <div class="form-group mb-2">
            <label>Investor</label>
            <select name="investor_id" class="form-control select-picker" data-live-search="true" required>
                <option value="">Select Investor</option>
                @foreach($investor as $inv)
                    <option value="{{ $inv->id }}">{{ $inv->name }} ({{ $inv->company }})</option>
                @endforeach
            </select>
        </div>

        <!-- Amount -->
        <div class="form-group mb-2">
            <label>Amount</label>
            <input type="number" name="amount" step="0.01" class="form-control" required>
        </div>

        <!-- Investment Type -->
        <div class="form-group mb-2">
            <label>Type</label>
            <select name="investment_type" class="form-control select-picker" data-live-search="true" required>
                <option value="">Select Investment Type</option>    
                <option value="investment">Investment</option>
                <option value="loan">Loan</option>
            </select>
        </div>

        <!-- Conditional Fields -->
        <div class="form-group mb-2 provideEmployeeDiv" style="display:none;">
            <label>Investor will provide Employee?</label>
            <input type="checkbox" name="provide_employee" value="1">
        </div>
        <div class="form-group mb-2 provideEmployeeDiv" style="display:none;">
            <label>Project</label>
            <select name="project_id" class="form-control select-picker" data-live-search="true">
                <option value="">Select Project</option>
                @foreach($projects as $pro)
                    <option value="{{ $pro->id }}">{{ $pro->project_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-2 provideEmployeeDiv" style="display:none;">
            <label>Profit Percent(%)</label>
            <input type="number" name="profit_percent" step="0.01" class="form-control">
        </div>
        <div class="form-group mb-2 provideEmployeeDiv" style="display:none;">
            <label>Investment Timeline</label>
            <input type="text" name="timeline" class="form-control">
        </div>
        <div class="form-group mb-2 provideEmployeeDiv" style="display:none;">
            <label>Investor Reference</label>
            <input type="text" name="refference" class="form-control">
        </div>

        <!-- Terms & Conditions -->
        <div class="form-group mb-2">
            <label>Terms & Conditions</label>
            <div id="termsWrapper">
                <div class="term-row input-group mb-1" data-index="0">
                    <span class="input-group-text">1</span>
                    <input type="text" name="terms[]" class="form-control" placeholder="Enter term..." required>
                    <button type="button" class="btn btn-outline-danger remove-term" style="display:none;">Remove</button>
                </div>
            </div>
            <button type="button" id="addTermBtn" class="btn btn-sm btn-primary mt-2">
                + Add More
            </button>
        </div>

        <!-- Hidden Template -->
        <div id="termTemplate" style="display:none;">
            <div class="term-row input-group mb-1" data-index="{index}">
                <span class="input-group-text">{num}</span>
                <input type="text" name="terms[]" class="form-control" placeholder="Enter term..." required disabled>
                <button type="button" class="btn btn-outline-danger remove-term">Remove</button>
            </div>
        </div>

        <!-- Transaction Type -->
        <div class="form-group mb-2">
            <label>Transaction Type</label>
            <select name="transaction_type" class="form-control select-picker" data-live-search="true" required>
                <option value="">Select Transaction Type</option>
                <option value="Dr">Debit (Dr)</option>
                <option value="Cr">Credit (Cr)</option>
            </select>
        </div>

        <!-- Bank -->
        <div class="form-group mb-2">
            <label>Bank</label>
            <select name="bank_id" class="form-control select-picker" data-live-search="true" required>
                <option value="">Select Bank</option>
                @foreach($banks as $bank)
                    <option value="{{ $bank->id }}">{{ $bank->bank_name }} - {{ $bank->account_number }}</option>
                @endforeach
            </select>
        </div>

        <!-- Note -->
        <div class="form-group mb-2">
            <label>Note</label>
            <textarea name="note" class="form-control"></textarea>
        </div>

        <button class="btn btn-success mt-2 mb-4">Save</button>
    </form>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {

    // Show/hide conditional fields
    function toggleProvideEmployee() {
        let type = $('select[name="investment_type"]').val();
        if (type === 'investment') {
            $('.provideEmployeeDiv').show();
        } else {
            $('.provideEmployeeDiv').hide();
        }
    }
    toggleProvideEmployee();
    $('select[name="investment_type"]').on('change', toggleProvideEmployee);

    // Terms & Conditions
    const $wrapper = $('#termsWrapper');
    const template = $('#termTemplate').html();
    let count = $wrapper.children('.term-row').length;

    // Add More Row
    $('#addTermBtn').on('click', function () {
        count++;
        let html = template.replaceAll('{index}', count - 1).replaceAll('{num}', count);
        let $row = $(html);
        $row.find('input').prop('disabled', false); // enable input before appending
        $wrapper.append($row);
        updateRemoveButtons();
    });

    // Remove Row
    $wrapper.on('click', '.remove-term', function () {
        $(this).closest('.term-row').remove();
        renumberRows();
        updateRemoveButtons();
    });

    function renumberRows() {
        $wrapper.find('.term-row').each(function (i) {
            $(this).attr('data-index', i);
            $(this).find('.input-group-text').text(i + 1);
        });
        count = $wrapper.find('.term-row').length;
    }

    function updateRemoveButtons() {
        if ($wrapper.find('.term-row').length <= 1) {
            $wrapper.find('.remove-term').hide();
        } else {
            $wrapper.find('.remove-term').show();
        }
    }

    updateRemoveButtons();
});
</script>
@endpush
