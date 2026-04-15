@extends('layouts.app')
@push('datatable-styles')
    <script src="{{ asset('vendor/jquery/frappe-charts.min.iife.js') }}"></script>
    @include('sections.datatable_css')
@endpush

    
    @section('filter-section')
        <form method="GET" action="{{ route('accounting-reports.ledger') }}" >
            <x-filters.filter-box>
                <!-- DATE START -->
                <div class="select-box d-flex pr-2 border-right-grey border-right-grey-sm-0">
                    <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.duration')</p>
                    <div class="select-status d-flex">
                        <input type="text" name="date_range"
                            class="position-relative text-dark form-control border-0 p-2 text-left f-14 f-w-500 border-additional-grey"
                            id="datatableRange2" value="{{ $date_range }}" placeholder="@lang('placeholders.dateRange')">
                    </div>
                </div>
                <!-- DATE END -->

                <button type="submit" class="btn btn-primary">Submit</button>
                

            </x-filters.filter-box>
            
        </form>


    @endsection
    @section('content')
    <div class="content-wrapper">
        <table id="accounts_table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Account</th>
                    <th>Description</th>
                    <th>Debit</th>
                    <th>Credit</th>
                </tr>
            </thead>
            <tbody>
                @php $total_debit = 0; $total_credit = 0; @endphp
                @foreach($ledger_entries as $entry)
                    @php
                        $total_debit += $entry['debit'];
                        $total_credit += $entry['credit'];
                    @endphp
                    <tr>
                        <td>{{ $entry['date'] }}</td>
                        <td>{{ $entry['account'] }}</td>
                        <td>{{ $entry['description'] }}</td>
                        <td>{{ number_format($entry['debit'], 2) }}</td>
                        <td>{{ number_format($entry['credit'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"><strong>Total</strong></td>
                    <td><strong>{{ number_format($total_debit, 2) }}</strong></td>
                    <td><strong>{{ number_format($total_credit, 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endsection


@push('scripts')
@include('sections.daterange_js')
<script type="text/javascript">
        function getDate() {
            $('#datatableRange2').daterangepicker({
                locale: daterangeLocale,
                linkedCalendars: false,
                showDropdowns: true,
                alwaysShowCalendars: true,
                minDate: moment().subtract(10, 'years'),
                maxDate: moment().add(10, 'years'),
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, function(start, end) {
                console.log("Selected range: " + start.format('YYYY-MM-DD') + " to " + end.format('YYYY-MM-DD'));
            });
        }

        $(function() {
            getDate();

            $('#datatableRange2').on('apply.daterangepicker', function(ev, picker) {
               
            });
        });

        $('#reset-filters').click(function() {
            $('#filter-form')[0].reset();
            $('#reset-filters').addClass('d-none');
        
        })
</script>

@endpush