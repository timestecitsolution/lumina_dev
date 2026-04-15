@extends('layouts.app')

<style>
    .hk-toggle {
        position: relative;
        display: inline-flex;
        align-items: center;
    }

    .hk-toggle input {
        display: none;
    }

    /* Material style */
    .hk-tg-3 label {
        width: 70px;
        height: 32px;
        background: #1f232b;
        border-radius: 50px;
        cursor: pointer;
        position: relative;
        transition: background .3s;
    }

    .hk-tg-3 label::after {
        content: '';
        width: 26px;
        height: 26px;
        background: #9aa0a6;
        border-radius: 50%;
        position: absolute;
        top: 3px;
        left: 4px;
        transition: 0.3s;
    }

    /* Checked */
    .hk-tg-3 input:checked + label {
        background: #1a73e8; /* green/blue active */
    }

    .hk-tg-3 input:checked + label::after {
        transform: translateX(38px);
        background: #fff;
    }
</style>


@push('datatable-styles')
    @include('sections.datatable_css')
@endpush
@section('filter-section')
<form id="filter-form">
    <x-filters.filter-box>
        <!-- DESIGNATION START -->
        <div class="select-box d-flex px-0 border-right-grey border-right-grey-sm-0 align-items-center" >
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.status')</p>
            <div class="select-filter-status">
                <select class="form-control select-picker" name="filter_status" id="filter_status" data-container="body">
                    <option selected value="all">@lang('app.all')</option>
                    <option value="yes">@lang('app.active')</option>
                    <option value="no">@lang('app.inactive')</option>
                </select>
            </div>
        </div>

        <div class="select-box d-flex px-0 border-right-grey border-right-grey-sm-0 align-items-center" >
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Project</p>
            <div class="select-filter-cotractor_type">
                <select class="form-control select-picker" name="filter_project" id="filter_project" data-container="body">
                    <option selected value="all">@lang('app.all')</option>

                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}">
                            {{ $project->project_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <!-- DESIGNATION END -->
        <!-- SEARCH BY TASK START -->
        <div class="task-search d-flex  py-1 px-lg-3 px-0 border-right-grey align-items-center">
            <form class="w-100 mr-1 mr-lg-0 mr-md-1 ml-md-1 ml-0 ml-lg-0">
                <div class="input-group bg-grey rounded">
                    <div class="input-group-prepend">
                        <span class="input-group-text border-0 bg-additional-grey">
                            <i class="fa fa-search f-13 text-dark-grey"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control f-14 p-1 border-additional-grey" id="search-text-field"
                           placeholder="@lang('app.startTyping')">
                </div>
            </form>
        </div>
        <!-- SEARCH BY TASK END -->

        <!-- RESET START -->
        <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
            <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                @lang('app.clearFilters')
            </x-forms.button-secondary>
        </div>
        <!-- RESET END -->

        <!-- MORE FILTERS START -->
        <!-- MORE FILTERS END -->
    </x-filters.filter-box>
</form>
@endsection
@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <x-forms.link-primary :link="route('sections.create')"  icon="plus">
                        Add Section
                    </x-forms.link-primary>
                   
        <div class="d-flex flex-column w-tables rounded mt-3 bg-white">

            {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}

        </div>
        
    </div>

@endsection

@push('scripts')
    @include('sections.datatable_js')

    <script>

        $('#filter_status').selectpicker();
        $('#filter_project').selectpicker();

        const showTable = () => {
            window.LaravelDataTables["section-table"].draw();
        };

        $('#section-table').on('preXhr.dt', function (e, settings, data) {
            data.searchText = $('#search-text-field').val();
            data.status = $('#filter_status').val();
            data.project = $('#filter_project').val();
        });

        // search + filter
        $('#search-text-field').on('keyup', function () {
            $('#reset-filters').removeClass('d-none');
            showTable();
        });

        $('#filter_status').on('change', function () {
            $('#reset-filters').removeClass('d-none');
            showTable();
        });

        $('#filter_project').on('change', function () {
            $('#reset-filters').removeClass('d-none');
            showTable();
        });

        // reset
        $('#reset-filters').on('click', function () {
            $('#filter-form')[0].reset();
            $('#filter_status').val('all').selectpicker('refresh');
            $('#filter_project').val('all').selectpicker('refresh');
            $('#reset-filters').addClass('d-none');
            showTable();
        });


        // Status toggle
        $(document).on('change', '.toggle-status', function () {

            let id = $(this).data('id');
            let status = $(this).is(':checked') ? 'yes' : 'no';

            $.easyAjax({
                url: "{{ url('account/sections') }}/" + id + "/toggle-status",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    status: status
                },
                success: function (response) {
                    if (response.status === 'success') {
                    }
                }
            });
        });


        // Delete row
        $(document).on('click', '.delete-row', function () {

            let url = $(this).data('url');

            Swal.fire({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.confirmDelete')",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "@lang('app.delete')",
                cancelButtonText: "@lang('app.cancel')",
            }).then((result) => {
                if (result.isConfirmed) {

                    $.easyAjax({
                        url: url,
                        type: "POST",   
                        blockUI: true,
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: "DELETE"  
                        },
                        success: function (response) {
                            if (response.status === 'success') {
                                window.LaravelDataTables["section-table"].draw();
                            }
                        }
                    });

                }
            });
        });






    </script>
@endpush
