@extends('layouts.app')

@section('content')
<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('app.view') @lang('payroll::app.menu.overtimeRequest')</h5>
    <button type="button" onclick="removeOpenModal()" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">

    <p>
        <x-employee :user="$employee"/>
    </p>

    <div class="col-12 px-0 pb-3 d-lg-flex">
        <p class="mb-0 text-lightest f-14 w-30 d-inline-block text-capitalize">
            @lang('app.startDate') </p>
        <p class="mb-0 text-dark-grey f-14">
            {{ $overtimeRequest->start_date->format(company()->date_format)  }}
        </p>

        <p class="mb-0 text-lightest f-14 w-30 ml-3 d-inline-block text-capitalize">
            @lang('app.endDate') </p>
        <p class="mb-0 text-dark-grey f-14">
            {{ $overtimeRequest->end_date->format(company()->date_format)  }}
        </p>

    </div>


    <div class="table-responsive">
        <x-table class="table-bordered" headType="thead-light">
            <x-slot name="thead">
                <th>#</th>
                <th>@lang('app.date')</th>
                <th>@lang('payroll::modules.payroll.overtimeHours')</th>
                <th>@lang('payroll::modules.payroll.clockedInHours')</th>
                <th>@lang('app.amount')</th>
            </x-slot>
            @php
                $payCode = $overtimeRequest->policy->payCode;
                $clockedHour = 0;
            @endphp

        </x-table>
    </div>

</div>
<div class="bg-white p-4 rounded">
                <h6>Approval Status</h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Priority</th>
                            <th>Employee</th>
                            <th>Action By</th>
                            <th>Status</th>
                            <th>Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($validation_permissions as $perm)
                            @php
                           
                                $approval = $overtime_request_approvals[$perm->employee_id] ?? null;
                                
                            @endphp
                            <tr>
                                <td>{{ $perm->priority }}</td>
                                <td>{{ $perm->employee->name ?? 'N/A' }} - ({{$perm->designation->name ?? 'N/A'}})</td>
                                <td>@if($approval){{ $approval->actionBy->name }}@endif</td>
                                <td>
                                    @if($approval)
                                        <span class="badge bg-success">{{ ucfirst($approval->action) }}</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $approval->comment ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @php
                    $isSuperAdmin = ($user->id == 1); 
                 
                @endphp

                @if($overtimeRequest->status == 'approved')
                    <span class="badge bg-success"><h4>Overtime Request is Approved</h4></span>
                @elseif($overtimeRequest->status == 'rejected')
                    <span class="badge bg-danger"><h4>Overtime Request is Rejected</h4></span>
                @elseif($isSuperAdmin || ($nextApproval && $nextApproval->employee_id == $user->id))
                    <form action="{{ route('overtime-requests.action', $overtimeRequest->id) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <textarea name="comment" class="form-control" placeholder="Optional comment"></textarea>
                        </div>
                        @if(!$isSuperAdmin)
                            <button type="submit" name="action" value="approved" class="btn btn-success">Approve</button>
                            <button type="submit" name="action" value="rejected" class="btn btn-danger">Reject</button>
                        @else
                            <button type="submit" name="action" value="approved" class="btn btn-success">Approve As @ {{ $nextApproval->employee->name }} - ({{$nextApproval->designation->name}})</button>
                            <button type="submit" name="action" value="rejected" class="btn btn-danger">Reject as @ {{ $nextApproval->employee->name }} - ({{$nextApproval->designation->name}})</button>

                        @endif
                        <input type="hidden" name="action_by" value="{{$user->id}}">
                        <input type="hidden" name="employee_id" value="{{$nextApproval->employee_id}}">
                        <input type="hidden" name="action_role" value="{{$nextApproval->designation_id}}">
                    </form>
                @else
                    <button class="btn btn-info">Pending Approval @ {{ $nextApproval->employee->name }} - ({{$nextApproval->designation->name}})</button>
                @endif
            </div>
@endsection
<!-- <script>

    $(MODAL_LG).on('click', '.react-button', function () {
    var id = {{ $overtimeRequest->id }};
    var type = $(this).data('type');
    var butonText = "@lang('payroll::messages.confirmAccept')";
    if(type != 'accept'){
        butonText = "@lang('payroll::messages.confirmReject')";
    }
    Swal.fire({
        title: "@lang('messages.sweetAlertTitle')",
        text: "@lang('payroll::messages.recoverRecord')",
        icon: 'warning',
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: butonText,
        cancelButtonText: "@lang('app.cancel')",
        customClass: {
            confirmButton: 'btn btn-primary mr-3',
            cancelButton: 'btn btn-secondary'
        },
        showClass: {
            popup: 'swal2-noanimation',
            backdrop: 'swal2-noanimation'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {

            var url = "{{ route('overtime-request-accept', ':id') }}?type="+type;
            url = url.replace(':id', id);

            $.easyAjax({
                type: 'GET',
                url: url,
                blockUI: true,
                success: function (response) {
                    if (response.status == "success") {
                        showTable();
                        $(MODAL_LG).modal('hide');
                    }
                }
            });
        }
    });
});
/* PAYROLL SALARY SCRIPTS */
</script> -->
