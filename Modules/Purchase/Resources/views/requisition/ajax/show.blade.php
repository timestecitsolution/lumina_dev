@extends('layouts.app')

@section('content')
    @if(session('success'))
        <x-alert type="success" icon="check-circle">
            {{ session('success') }}
        </x-alert>
    @endif
    <div class="content-wrapper">
        <div class="bg-white rounded b-shadow-4 create-inv">
            <div class="bg-white p-4 rounded">
                <h5>Requisition No: {{ $requisition->req_no }}</h5>
                <p><strong>Project:</strong> {{ $requisition->project->project_name }}</p>
                <p><strong>Delivery Date:</strong> {{ $requisition->delivery_date }}</p>
                <p><strong>Delivery Place:</strong> {{ $requisition->delivery_place }}</p>
                <p><strong>Note:</strong> {{ $requisition->note }}</p>

                <hr>

                <h6>Items</h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requisition->items as $i => $item)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $item->item_name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->unit }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
                           
                                $approval = $requisition_approvals[$perm->employee_id] ?? null;
                                
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

                @if($requisition->status == 'approved')
                    <span class="badge bg-success"><h4>Requisition is Approved</h4></span>
                @elseif($requisition->status == 'rejected')
                    <span class="badge bg-danger"><h4>Requisition is Rejected Please Edit</h4></span>
                @elseif($isSuperAdmin || ($nextApproval && $nextApproval->employee_id == $user->id))
                    <form action="{{ route('requisitions.action', $requisition->id) }}" method="POST">
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
        </div>
    </div>

@endsection

