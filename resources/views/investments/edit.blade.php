@extends('layouts.app')
@section('content')

<div class="container">
    <h3>Edit Investment</h3>

    <form action="{{ route('investments.update', $investment->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Date -->
        <div class="form-group mb-2">
            <label>Date</label>
            <input type="date" name="date" value="{{ \Carbon\Carbon::parse($investment->date)->format('Y-m-d') }}" class="form-control" required>
        </div>

        <!-- Investment Name -->
        <div class="form-group mb-2">
            <label>Investment Name</label>
            <input type="text" name="investment_name" value="{{ $investment->investment_name }}" class="form-control" required>
        </div>

        <!-- Investor -->
        <div class="form-group mb-2">
            <label>Investor</label>
            <select name="investor_id" class="form-control select-picker" data-live-search="true">
                @foreach($investor as $inv)
                    <option value="{{ $inv->id }}" {{ $investment->investor_id == $inv->id ? 'selected' : '' }}>
                        {{ $inv->name }} ({{ $inv->company }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Amount -->
        <div class="form-group mb-2">
            <label>Amount</label>
            <input type="number" name="amount" step="0.01" value="{{ $investment->amount }}" class="form-control" required>
        </div>

        <!-- Investment Type -->
        <div class="form-group mb-2">
            <label>Type</label>
            <select name="investment_type" class="form-control select-picker" required>
                <option value="investment" {{ $investment->investment_type=='investment'?'selected':'' }}>Investment</option>
                <option value="loan" {{ $investment->investment_type=='loan'?'selected':'' }}>Loan</option>
            </select>
        </div>

        <!-- Conditional Fields -->
        <div class="form-group mb-2 provideEmployeeDiv" style="{{ $investment->investment_type=='investment'?'':'display:none;' }}">
            <label>Investor will provide Employee?</label>
            <input type="checkbox" name="provide_employee" value="1" {{ $investment->provide_employee==1?'checked':'' }}>
        </div>

        <div class="form-group mb-2 provideEmployeeDiv" style="{{ $investment->investment_type=='investment'?'':'display:none;' }}">
            <label>Project</label>
            <select name="project_id" class="form-control select-picker">
                <option value="">Select</option>
                @foreach($projects as $pro)
                    <option value="{{ $pro->id }}" {{ $investment->project_id==$pro->id?'selected':'' }}>
                        {{ $pro->project_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-2 provideEmployeeDiv" style="{{ $investment->investment_type=='investment'?'':'display:none;' }}">
            <label>Profit Percent(%)</label>
            <input type="number" step="0.01" name="profit_percent" value="{{ $investment->profit_percent }}" class="form-control">
        </div>

        <div class="form-group mb-2 provideEmployeeDiv" style="{{ $investment->investment_type=='investment'?'':'display:none;' }}">
            <label>Investment Timeline</label>
            <input type="text" name="timeline" value="{{ $investment->timeline }}" class="form-control">
        </div>

        <div class="form-group mb-2 provideEmployeeDiv" style="{{ $investment->investment_type=='investment'?'':'display:none;' }}">
            <label>Investor Reference</label>
            <input type="text" name="refference" value="{{ $investment->refference }}" class="form-control">
        </div>

        <!-- Terms & Conditions -->
        <div class="form-group mb-2">
            <label>Terms & Conditions</label>

            <div id="termsWrapper">
                @foreach($investment->terms as $index => $t)
                    <div class="term-row input-group mb-1" data-index="{{ $index }}">
                        <span class="input-group-text">{{ $index + 1 }}</span>
                        <input type="text" name="terms[]" class="form-control" value="{{ $t->term }}">
                        <button type="button" class="btn btn-outline-danger remove-term" {{ $index==0?'style=display:none;':'' }}>Remove</button>
                    </div>
                @endforeach
            </div>

            <button type="button" id="addTermBtn" class="btn btn-sm btn-primary mt-2">+ Add More</button>
        </div>

        <!-- Hidden Template -->
        <div id="termTemplate" style="display:none;">
            <div class="term-row input-group mb-1" data-index="{index}">
                <span class="input-group-text">{num}</span>
                <input type="text" name="terms[]" class="form-control" placeholder="Enter term...">
                <button type="button" class="btn btn-outline-danger remove-term">Remove</button>
            </div>
        </div>

        <!-- Transaction Type -->
        <div class="form-group mb-2">
            <label>Transaction Type</label>
            <select name="transaction_type" class="form-control select-picker" required>
                <option value="dr" {{ $investment->transaction_type=='dr'?'selected':'' }}>Debit (Dr)</option>
                <option value="cr" {{ $investment->transaction_type=='cr'?'selected':'' }}>Credit (Cr)</option>
            </select>
        </div>

        <!-- Bank -->
        <div class="form-group mb-2">
            <label>Bank</label>
            <select name="bank_id" class="form-control select-picker">
                @foreach($banks as $bank)
                    <option value="{{ $bank->id }}" {{ $investment->bank_id==$bank->id?'selected':'' }}>
                        {{ $bank->bank_name }}-{{ $bank->account_number }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Note -->
        <div class="form-group mb-2">
            <label>Note</label>
            <textarea name="note" class="form-control">{{ $investment->note }}</textarea>
        </div>

        <button class="btn btn-success mt-2 mb-4">Update</button>
    </form>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {

    // Toggle conditional fields
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

    // TERMS JS
    const $wrapper = $('#termsWrapper');
    const template = $('#termTemplate').html();
    let count = $wrapper.children('.term-row').length;

    function updateRequired() {
        $wrapper.find('input[name="terms[]"]').each(function() {
            $(this).attr('required', true);
        });
    }
    updateRequired();

    $('#addTermBtn').on('click', function () {
        count++;
        let html = template.replaceAll('{index}', count - 1).replaceAll('{num}', count);
        $wrapper.append(html);
        updateRemoveButtons();
        updateRequired();
    });

    $wrapper.on('click', '.remove-term', function () {
        $(this).closest('.term-row').remove();
        renumberRows();
        updateRemoveButtons();
        updateRequired();
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
