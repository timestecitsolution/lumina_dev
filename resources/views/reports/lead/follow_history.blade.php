@extends('layouts.app')
@push('datatable-styles')
    @include('sections.datatable_css')
@endpush
@section('filter-section')
    <div class="d-flex filter-box project-header bg-white border-bottom">

        <div class="mobile-close-overlay w-100 h-100" id="close-client-overlay"></div>
        <div class="project-menu d-lg-flex" id="mob-client-detail">
            <a class="d-none close-it" href="javascript:;" id="close-client-detail">
                <i class="fa fa-times"></i>
            </a>
            <x-tab :href="route('lead-report.index') . '?tab=profile'" :text="__('modules.deal.profile')" class="profile" />
            <x-tab :href="route('lead-report.chart') . '?tab=chart'" :text="__('modules.leadContact.leadReport')" class="chart" />
            <x-tab :href="route('lead-report.visit') . '?tab=visit'" :text="__('modules.leadContact.visitHistory')" class="visit" />
            <x-tab :href="route('lead-report.follow') . '?tab=follow'" :text="__('modules.leadContact.followHistory')" class="follow" />


        </div>
    </div>

    <x-filters.filter-box>
            <!-- DATE START -->
            <div class="select-box d-flex pr-2 border-right-grey border-right-grey-sm-0">
                <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.duration')</p>
                <div class="select-status d-flex">
                    <input type="text" class="position-relative text-dark form-control border-0 p-2 text-left f-14 f-w-500 border-additional-grey"
                        id="datatableRange4" placeholder="@lang('placeholders.dateRange')">
                </div>
            </div>

            <!-- DATE END -->

            <!-- CLIENT START -->
            <div class="select-box d-flex  py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
                <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.leadAgent')</p>
                <div class="select-status">
                    <select class="form-control select-picker" name="agent" id="agent" data-live-search="true"
                        data-size="8">
                        <option value="all">@lang('app.all')</option>
                        @foreach ($sales_man as $agent) 
                       
                            <option value="{{$agent->id}}">{{$agent->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- CLIENT END -->


            <!-- CLIENT START -->
            <div class="select-box d-flex  py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
                <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.lead')</p>
                <div class="select-status">
                    <select class="form-control select-picker" name="lead" id="lead" data-live-search="true"
                        data-size="8">
                        <option value="all">@lang('app.all')</option>
                        @foreach ($leads as $lead) 
                            <option value="{{$lead->id}}">{{$lead->client_name}}-{{$lead->mobile}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- CLIENT END -->

            <!-- RESET START -->
            <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
                <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                    @lang('app.clearFilters')
                </x-forms.button-secondary>
            </div>
            <!-- RESET END -->

        </x-filters.filter-box>
@endsection

@section('content')
    <div class="content-wrapper">

        <!-- ROW START -->
        <div class="row pb-5">
            <div class="col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4">
                <!-- Add Task Export Buttons Start -->
                <div class="d-flex" id="table-actions">

                </div>
                <!-- Add Task Export Buttons End -->
                <!-- Task Box Start -->
                <div class="d-flex flex-column w-tables rounded mt-3 bg-white">

                    {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}

                </div>
                <!-- Task Box End -->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/graph/frappechart.js') }}"></script>
    <script src="{{ asset('vendor/jquery/Chart.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery/frappe-charts.min.iife.js') }}"></script>

    @include('sections.datatable_js')
    <script type="text/javascript">
        const activeTab = "{{ $activeTab }}";
        $('.project-menu .' + activeTab).addClass('active');

        function getDate() {
            var start = moment().clone().startOf('month');
            var end = moment();

            $('#datatableRange4').daterangepicker({
                locale: daterangeLocale,
                linkedCalendars: false,
                startDate: start,
                endDate: end,
                ranges: daterangeConfig
            }, cb);
        }
        $(function() {
            getDate()
            $('#datatableRange4').on('apply.daterangepicker', function(ev, picker) {
                showTable();
            });

        });

        $('#lead-follow-table').on('preXhr.dt', function(e, settings, data) {

            var dateRangePicker = $('#datatableRange4').data('daterangepicker');
            var startDate = $('#datatableRange4').val();

            if (startDate == '') {
                startDate = null;
                endDate = null;
            } else {
                startDate = dateRangePicker.startDate.format('{{ company()->moment_date_format }}');
                endDate = dateRangePicker.endDate.format('{{ company()->moment_date_format }}');
            }

            var agent = $('#agent').val();
            var lead = $('#lead').val();

            data['startDate'] = startDate;
            data['endDate'] = endDate;
            data['agent'] = agent;
            data['lead'] = lead;

        });
        const showTable = () => {
            window.LaravelDataTables["lead-follow-table"].draw(true);
        }

        $('#agent').on('change keyup',
            function() {
                if ($('#agent').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                    showTable();
                } else {
                    $('#reset-filters').addClass('d-none');
                    showTable();
                }
            });

        $('#lead').on('change keyup',
            function() {
                if ($('#lead').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                    showTable();
                } else {
                    $('#reset-filters').addClass('d-none');
                    showTable();
                }
            });

        $('#reset-filters').click(function() {
            $('#filter-form')[0].reset();
            // getDate()

            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            showTable();
        });

        $('#reset-filters-2').click(function() {
            $('#filter-form')[0].reset();

            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            showTable();
        });
    </script>
@endpush
