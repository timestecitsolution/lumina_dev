<div class="row">
    <div class="col-sm-12">

        <div class="card">
            <div class="card-body">

                {{-- Contractor Type --}}
                <div class="mb-6">
                    <strong>@lang('modules.contractor.contractorType'):</strong><br>
                    {{ $contractor->type->type_name ?? '-' }}
                </div>

                {{-- Name --}}
                <div class="mb-6">
                    <strong>@lang('modules.contractor.name'):</strong><br>
                    {{ $contractor->name }}
                </div>

                {{-- Phone --}}
                <div class="mb-6">
                    <strong>@lang('modules.contractor.phone'):</strong><br>
                    {{ $contractor->phone }}
                </div>

                {{-- Address --}}
                <div class="mb-6">
                    <strong>@lang('app.address'):</strong><br>
                    {!! $contractor->address ?? '-' !!}
                </div>

                {{-- TIN --}}
                <div class="mb-6">
                    <strong>@lang('modules.contractor.tin'):</strong><br>
                    {{ $contractor->tin ?? '-' }}
                </div>

                {{-- BIN --}}
                <div class="mb-6">
                    <strong>@lang('modules.contractor.bin'):</strong><br>
                    {{ $contractor->bin ?? '-' }}
                </div>

                {{-- Trade License No --}}
                <div class="mb-6">
                    <strong>@lang('modules.contractor.trade_license_no'):</strong><br>
                    {{ $contractor->trade_license_no ?? '-' }}
                </div>

                {{-- Trade License Image --}}
                <div class="mb-6">
                    <strong>@lang('modules.contractor.trade_license_img'):</strong><br>
                    @if($contractor->trade_license_img)
                        <img src="{{ asset('storage/'.$contractor->trade_license_img) }}"
                             class="img-thumbnail"
                             style="max-height:150px">
                        <br>
                        <a href="{{ asset('storage/'.$contractor->trade_license_img) }}" 
                           class="btn btn-sm btn-primary mt-1" 
                           download>
                            Download
                        </a>
                    @else
                        <span>-</span>
                    @endif
                </div>

                {{-- NID --}}
                <div class="mb-6">
                    <strong>@lang('modules.contractor.nid'):</strong><br>
                    {{ $contractor->nid ?? '-' }}
                </div>

                {{-- NID Image --}}
                <div class="mb-6">
                    <strong>@lang('modules.contractor.nid_iamge'):</strong><br>
                    @if($contractor->nid_img)
                        <img src="{{ asset('storage/'.$contractor->nid_img) }}"
                             class="img-thumbnail"
                             style="max-height:150px">
                        <br>
                        <a href="{{ asset('storage/'.$contractor->nid_img) }}" 
                           class="btn btn-sm btn-primary mt-1" 
                           download>
                            Download
                        </a>
                    @else
                        <span>-</span>
                    @endif
                </div>

                {{-- Profile Image --}}
                <div class="mb-6">
                    <strong>@lang('modules.contractor.profileImage'):</strong><br>
                    @if($contractor->profile_img)
                        <img src="{{ asset('storage/'.$contractor->profile_img) }}"
                             class="img-thumbnail rounded-circle"
                             style="max-height:150px">
                        <br>
                        <a href="{{ asset('storage/'.$contractor->profile_img) }}" 
                           class="btn btn-sm btn-primary mt-1" 
                           download>
                            Download
                        </a>
                    @else
                        <span>-</span>
                    @endif
                </div>

                {{-- Status --}}
                <div class="mb-6">
                    <strong>@lang('app.status'):</strong><br>
                    @if($contractor->status == 1)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-danger">Inactive</span>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="mt-4">
                    <a href="{{ route('contractors.edit', $contractor->id) }}"
                       class="btn btn-warning">
                        Edit
                    </a>

                    <a href="{{ route('contractors.index') }}"
                       class="btn btn-secondary">
                        Back
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>
