<div class="row">
    <div class="col-sm-12">
        <x-form id="update-contractor-type-form">

            <div class="form-group">
                <x-forms.text
                    fieldId="type_name"
                    :fieldLabel="__('modules.contractor.typeName')"
                    fieldName="type_name"
                    fieldRequired="true"
                    :fieldValue="$contractorType->type_name"
                />
            </div>

            <div class="form-group">
                <x-forms.label fieldId="description" :fieldLabel="__('app.description')" />
                <div id="description">{!! $contractorType->description !!}</div>
                <textarea name="description" id="description-text" class="d-none"></textarea>
            </div>

            <div class="form-group">
                <x-forms.select
                    fieldId="status"
                    :fieldLabel="__('app.status')"
                    fieldName="status">
                    <option value="yes" {{ $contractorType->status == 'yes' ? 'selected' : '' }}>
                        @lang('app.active')
                    </option>
                    <option value="no" {{ $contractorType->status == 'no' ? 'selected' : '' }}>
                        @lang('app.inactive')
                    </option>
                </x-forms.select>
            </div>

            <x-form-actions>
                <x-forms.button-primary id="update-contractor-type">
                    @lang('app.update')
                </x-forms.button-primary>
                <x-forms.button-cancel data-dismiss="modal">
                    @lang('app.cancel')
                </x-forms.button-cancel>
            </x-form-actions>

        </x-form>
    </div>
</div>

<script>
$(document).ready(function () {

    quillImageLoad('#description');

    $('#update-contractor-type').click(function () {

        let note = document.getElementById('description').children[0].innerHTML;
        document.getElementById('description-text').value = note;

        const url = "{{ route('contractor-types.update', $contractorType->id) }}";

        $.easyAjax({
            url: url,
            container: '#update-contractor-type-form',
            type: "PUT",
            blockUI: true,
            data: $('#update-contractor-type-form').serialize(),
            success: function (response) {
                if (response.status === 'success') {
                    window.location.href = "{{ route('contractor-types.index') }}";
                }
            }
        });
    });
});
</script>

