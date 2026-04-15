<div class="row">
    <div class="col-sm-12">

        {{-- Basic Info Table --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <tbody>

                <tr>
                    <th width="25%">Deed Name</th>
                    <td>{{ $deed->deed_name }}</td>
                </tr>

                <tr>
                    <th>Project</th>
                    <td>{{ $deed->project->project_name ?? '-' }}</td>
                </tr>

                <tr>
                    <th>Contractor</th>
                    <td>
                        {{ $deed->contractor->name ?? '-' }}
                        @if($deed->contractor && $deed->contractor->type)
                            ({{ $deed->contractor->type->type_name }})
                        @endif
                    </td>
                </tr>

                <tr>
                    <th>Deed Date</th>
                    <td>
                        {{ \Carbon\Carbon::parse($deed->deed_date)->format(company()->date_format) }}
                    </td>
                </tr>

                <tr>
                    <th>File</th>
                    <td>
                        @if($deed->deed_file)
                            <a class="btn btn-sm btn-outline-primary" 
                               href="{{ asset('storage/'.$deed->deed_file) }}" 
                               target="_blank">
                                <i class="fa fa-eye"></i> Preview File
                            </a>
                        @else
                            <span class="text-muted">No file uploaded</span>
                        @endif
                    </td>
                </tr>

                <tr>
                    <th>Status</th>
                    <td>
                        @if($deed->status == 'Yes')
                            <span class="badge bg-success px-3 py-2">Active</span>
                        @else
                            <span class="badge bg-danger px-3 py-2">Inactive</span>
                        @endif
                    </td>
                </tr>

                </tbody>
            </table>
        </div>

        <hr>

        {{-- Sections --}}
        @foreach($deed->details as $detail)
            <div class="card p-3 mb-3">
                <h6>Section: {{ $detail->section->section_name ?? '-' }}</h6>
                <div class="row mt-2">
                    <div class="col-md-3">
                        <label>Unit Type:</label>
                        <p class="form-control-plaintext">{{ $detail->unit_type }}</p>
                    </div>
                    <div class="col-md-2">
                        <label>Per Unit Rate:</label>
                        <p class="form-control-plaintext">{{ $detail->per_unit_rate }}</p>
                    </div>
                    <div class="col-md-2">
                        <label>Total Unit:</label>
                        <p class="form-control-plaintext">{{ $detail->total_unit }}</p>
                    </div>
                    <div class="col-md-3">
                        <label>Section Amount:</label>
                        <p class="form-control-plaintext">{{ $detail->section_amount }}</p>
                    </div>
                </div>

                {{-- Steps --}}
                @if($detail->steps->count())
                    <div class="mt-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Step Name</th>
                                    <th>Percentage</th>
                                    <th>Budget</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detail->steps as $step)
                                    <tr>
                                        <td>{{ $step->step->step_name ?? '-' }}</td>
                                        <td>{{ $step->budget_amount_percentage }}%</td>
                                        <td>{{ $step->budget_amount }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endforeach

        <h4 class="text-right mt-3">Grand Total: {{ $deed->deed_total_amount }}</h4>

    </div>
</div>
