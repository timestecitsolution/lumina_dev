@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@php
    $addPermission = user()->permission('add_requisition');
@endphp

@section('filter-section')

<x-filters.filter-box>

    <!-- DATE FILTER -->
    <div class="select-box d-flex pr-2 border-right-grey">
        <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">
            @lang('app.duration')
        </p>
        <input type="text" class="form-control border-0 p-2"
               id="datatableRange" placeholder="@lang('placeholders.dateRange')">
    </div>

    <!-- SEARCH -->
    <div class="task-search d-flex py-1 px-lg-3 border-right-grey align-items-center">
        <div class="input-group bg-grey rounded">
            <span class="input-group-text bg-additional-grey border-0">
                <i class="fa fa-search f-13 text-dark-grey"></i>
            </span>
            <input type="text" class="form-control f-14 border-additional-grey"
                   id="search-text-field"
                   placeholder="@lang('app.startTyping')">
        </div>
    </div>

    <!-- RESET -->
    <div class="select-box d-flex py-1 px-lg-2">
        <x-forms.button-secondary class="btn-xs d-none"
            id="reset-filters" icon="times-circle">
            @lang('app.clearFilters')
        </x-forms.button-secondary>
    </div>

    <!-- MORE FILTERS -->
    <x-filters.more-filter-box>

        <div class="more-filter-items">
            <label class="f-14 text-dark-grey mb-12">@lang('app.status')</label>
            <select class="form-control select-picker"
                    id="status" data-container="body">
                <option value="all">@lang('app.all')</option>
                <option value="pending">@lang('app.pending')</option>
                <option value="approved">@lang('app.approved')</option>
                <option value="rejected">@lang('app.rejected')</option>
            </select>
        </div>

    </x-filters.more-filter-box>

</x-filters.filter-box>
@endsection

@section('content')
<div class="content-wrapper">

    <div class="d-flex justify-content-between action-bar">

        <div id="table-actions">
            <x-forms.link-primary :link="route('requisitions.create')" class="mr-3" icon="plus">
            <!-- openRightModal float-left -->
                @lang('purchase::app.requisition.create_requisistion')
            </x-forms.link-primary>
        </div>

    </div>

    <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
        {!! $dataTable->table(['class' => 'table table-hover w-100'], true) !!}
    </div>

</div>
@endsection

@push('scripts')
@include('sections.datatable_js')

<script>
$('#requisitions-table').on('preXhr.dt', function (e, settings, data) {

    let picker = $('#datatableRange').data('daterangepicker');
    let startDate = null;
    let endDate = null;

    if (picker && $('#datatableRange').val() !== '') {
        startDate = picker.startDate.format('{{ $company->moment_format }}');
        endDate = picker.endDate.format('{{ $company->moment_format }}');
    }

    data.startDate  = startDate;
    data.endDate    = endDate;
    data.searchText = $('#search-text-field').val();
    data.status     = $('#status').val();
});

const reloadTable = () => {
    window.LaravelDataTables["requisitions-table"].draw(true);
};

$('#search-text-field, #status').on('keyup change', function () {
    $('#reset-filters').toggleClass(
        'd-none',
        !($('#search-text-field').val() || $('#status').val() !== 'all')
    );
    reloadTable();
});

$('body').on('click', '#reset-filters', function () {
    $('#datatableRange').val('');
    $('#search-text-field').val('');
    $('#status').val('all').selectpicker('refresh');
    $(this).addClass('d-none');
    reloadTable();
});
</script>
@endpush
