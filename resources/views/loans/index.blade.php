@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('filter-section')
    <x-filters.filter-box>
        <div class="select-box d-flex px-0 border-right-grey align-items-center">
            <p class="mb-0 pr-2 f-14 text-dark-grey">@lang('app.status')</p>
            <div class="select-filter-status">
                <select class="form-control select-picker" name="filter_status" id="filter_status" data-container="body">
                    <option selected value="all">@lang('app.all')</option>
                    <option value="requested">@lang('Requested')</option>
                    <option value="approved">@lang('Approved')</option>
                    <option value="disbursed">@lang('Disbursed')</option>
                    <option value="closed">@lang('Closed')</option>
                    <option value="rejected">@lang('Rejected')</option>
                </select>
            </div>
        </div>

        <div class="task-search d-flex py-1 px-lg-3 px-0 border-right-grey align-items-center">
            <form class="w-100 mr-1">
                <div class="input-group bg-grey rounded">
                    <div class="input-group-prepend">
                        <span class="input-group-text border-0 bg-additional-grey">
                            <i class="fa fa-search f-13 text-dark-grey"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control f-14 p-1 border-additional-grey"
                        id="search-text-field" placeholder="@lang('app.startTyping')">
                </div>
            </form>
        </div>

        <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
            <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                @lang('app.clearFilters')
            </x-forms.button-secondary>
        </div>
    </x-filters.filter-box>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="d-grid d-lg-flex d-md-flex action-bar">
        <div id="table-actions" class="flex-grow-1 align-items-center">
            <x-forms.link-primary :link="route('loan.create')" class="mr-3 float-left" icon="plus">
                @lang('Add Loan')
            </x-forms.link-primary>
        </div>

        <x-datatable.actions>
            <div class="select-status mr-3 pl-3">
                <select name="action_type" class="form-control select-picker" id="quick-action-type" disabled>
                    <option value="">@lang('app.selectAction')</option>
                    <option value="delete">@lang('app.delete')</option>
                </select>
            </div>
        </x-datatable.actions>
    </div>

    <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
        {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}
    </div>
</div>
@endsection

@push('scripts')
    @include('sections.datatable_js')

    <script>
        $('#filter_status').selectpicker();

        const showTable = () => {
            window.LaravelDataTables["employee-loans-table"].draw(true);
        }

        $('#employee-loans-table').on('preXhr.dt', function(e, settings, data) {
            data['searchText'] = $('#search-text-field').val();
            data['status'] = $('#filter_status').val();
        });

        $('#search-text-field, #filter_status').on('change keyup', function() {
            if ($('#filter_status').val() !== "all" || $('#search-text-field').val() !== "") {
                $('#reset-filters').removeClass('d-none');
            } else {
                $('#reset-filters').addClass('d-none');
            }
            showTable();
        });

        $('#reset-filters').click(function() {
            $('#filter_status').val('all');
            $('#search-text-field').val('');
            $('.select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            showTable();
        });
    </script>
@endpush
