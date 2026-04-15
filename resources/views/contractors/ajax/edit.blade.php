<div class="row">
    <div class="col-sm-12">
        <x-form id="save-contractor-form" enctype="multipart/form-data">
            @csrf
            @if(isset($contractor))
                @method('PUT')
            @endif
            {{-- Contractor Type --}}
            <div class="form-group">
                <x-forms.select
                    fieldId="contractor_type_id"
                    :fieldLabel="__('modules.contractor.contractorType')"
                    fieldName="contractor_type_id"
                    fieldRequired="true">
                    <option value="">@lang('app.select')</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}"
                            @if(isset($contractor) && $contractor->contractor_type_id == $type->id) selected @endif>
                            {{ $type->type_name }}
                        </option>
                    @endforeach
                </x-forms.select>
            </div>

            {{-- Name --}}
            <div class="form-group">
                <x-forms.text
                    fieldId="name"
                    :fieldLabel="__('modules.contractor.name')"
                    fieldName="name"
                    fieldRequired="true"
                    :fieldValue="$contractor->name ?? ''"
                />
            </div>

            {{-- Phone --}}
            <div class="form-group">
                <x-forms.text
                    fieldId="phone"
                    :fieldLabel="__('modules.contractor.phone')"
                    fieldName="phone"
                    fieldRequired="true"
                    :fieldValue="$contractor->phone ?? ''"
                />
            </div>

            {{-- Address --}}
            <div class="form-group">
                <x-forms.label fieldId="address" :fieldLabel="__('app.address')" />
                <div id="address">{!! $contractor->address ?? '' !!}</div>
                <textarea name="address" id="address-text" class="d-none"></textarea>
            </div>

            {{-- tin --}}
            <div class="form-group">
                <x-forms.text
                    fieldId="tin"
                    :fieldLabel="__('modules.contractor.tin')"
                    fieldName="tin"
                    :fieldValue="$contractor->tin ?? ''"
                />
            </div>

            {{-- bin --}}
            <div class="form-group">
                <x-forms.text
                    fieldId="bin"
                    :fieldLabel="__('modules.contractor.bin')"
                    fieldName="bin"
                    :fieldValue="$contractor->bin ?? ''"
                />
            </div>

            {{-- trade_license_no --}}
            <div class="form-group">
                <x-forms.text
                    fieldId="trade_license_no"
                    :fieldLabel="__('modules.contractor.trade_license_no')"
                    fieldName="trade_license_no"
                    :fieldValue="$contractor->trade_license_no ?? ''"
                />
            </div>

            
            {{-- Trade License --}}
            <div class="form-group">
                <x-forms.file
                    fieldId="trade_license_img"
                    :fieldLabel="__('modules.contractor.trade_license_img')"
                    fieldName="trade_license_img"
                />

                @if(isset($contractor) && $contractor->trade_license_img)
                    <div class="mt-2">
                        <img src="{{ asset('storage/'.$contractor->trade_license_img) }}"
                             class="img-thumbnail"
                             style="max-height:120px">
                    </div>
                @endif
            </div>


            {{-- nid --}}
            <div class="form-group">
                <x-forms.text
                    fieldId="nid"
                    :fieldLabel="__('modules.contractor.nid')"
                    fieldName="nid"
                    :fieldValue="$contractor->nid ?? ''"
                />
            </div>

            {{-- NID --}}
            <div class="form-group">
                <x-forms.file
                    fieldId="nid_img"
                    :fieldLabel="__('modules.contractor.nid_iamge')"
                    fieldName="nid_img"
                />

                @if(isset($contractor) && $contractor->nid_img)
                    <div class="mt-2">
                        <img src="{{ asset('storage/'.$contractor->nid_img) }}"
                             class="img-thumbnail"
                             style="max-height:120px">
                    </div>
                @endif
            </div>


            {{-- Profile --}}
            <div class="form-group">
                <x-forms.file
                    fieldId="profile_img"
                    :fieldLabel="__('modules.contractor.profileImage')"
                    fieldName="profile_img"
                />

                @if(isset($contractor) && $contractor->profile_img)
                    <div class="mt-2">
                        <img src="{{ asset('storage/'.$contractor->profile_img) }}"
                             class="img-thumbnail rounded-circle"
                             style="max-height:120px">
                    </div>
                @endif
            </div>


            {{-- Status --}}
            <div class="form-group">
                <x-forms.select
                    fieldId="status"
                    :fieldLabel="__('app.status')"
                    fieldName="status">
                    <option value="1" @if(($contractor->status ?? 1) == 1) selected @endif>
                        @lang('app.active')
                    </option>
                    <option value="0" @if(($contractor->status ?? 1) == 0) selected @endif>
                        @lang('app.inactive')
                    </option>
                </x-forms.select>
            </div>

            <x-form-actions>
                <x-forms.button-primary id="save-contractor">
                    @lang('app.save')
                </x-forms.button-primary>
                <x-forms.button-cancel data-dismiss="modal">
                    @lang('app.cancel')
                </x-forms.button-cancel>
            </x-form-actions>

        </x-form>
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function () {

        quillImageLoad('#address');

        $('input[type="file"]').on('change', function (e) {
            let input = this;
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    $(input).closest('.form-group')
                        .find('img')
                        .attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        });

        $('#save-contractor').click(function () {

            let address = document.getElementById('address').children[0].innerHTML;
            document.getElementById('address-text').value = address;

            let url = "{{ isset($contractor) ? route('contractors.update', $contractor->id) : route('contractors.store') }}";
            let method = "{{ isset($contractor) ? 'PUT' : 'POST' }}";

            $.easyAjax({
                url: url,
                container: '#save-contractor-form',
                type: 'POST', 
                blockUI: true,
                file: true,
                data: $('#save-contractor-form').serialize() + '&_method=' + method,
                success: function (response) {
                    if (response.status === 'success') {
                        window.location.href = "{{ route('contractors.index') }}";
                    }
                }
            });
        });


    });


</script>
