@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('filter-section')
    <x-filters.filter-box>
        
        <div class="task-search d-flex py-1 px-lg-3 px-0 border-right-grey align-items-center">
            <form class="w-100 mr-1">
                <div class="input-group bg-grey rounded">
                    <div class="input-group-prepend">
                        <span class="input-group-text border-0 bg-additional-grey">
                            <i class="fa fa-search f-13 text-dark-grey"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control f-14 p-1 border-additional-grey"
                        id="search-text-field" placeholder="Search Investor Name, Phone, Email">
                </div>
            </form>
        </div>

        <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
            <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                Clear Filters
            </x-forms.button-secondary>
        </div>

    </x-filters.filter-box>
@endsection


@section('content')
<div class="content-wrapper">

    <div class="d-grid d-lg-flex d-md-flex action-bar">
        <div id="table-actions">
            <x-forms.link-primary :link="route('investors.create')" class="mr-3" icon="plus">
                Add Investor
            </x-forms.link-primary>
        </div>
    </div>

    <div class="d-flex flex-column w-tables rounded bg-white mt-3">
        {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}
    </div>

</div>
@endsection


@push('scripts')
@include('sections.datatable_js')

<script>
    const showTable = () => {
        window.LaravelDataTables["investors-table"].draw(true);
    }

    $('#investors-table').on('preXhr.dt', function(e, settings, data) {
        data['searchText'] = $('#search-text-field').val();
    });

    $('#search-text-field').on('keyup change', function() {
        if ($(this).val() !== "") {
            $('#reset-filters').removeClass('d-none');
        } else {
            $('#reset-filters').addClass('d-none');
        }
        showTable();
    });

    $('#reset-filters').click(function() {
        $('#search-text-field').val('');
        $('#reset-filters').addClass('d-none');
        showTable();
    });
</script>
@endpush
