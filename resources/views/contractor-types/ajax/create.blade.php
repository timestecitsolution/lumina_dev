<div class="row">
    <div class="col-sm-12">
        <x-form id="save-contractor-type-form">

            <div class="form-group">
                <x-forms.text
                    fieldId="type_name"
                    :fieldLabel="__('modules.contractor.typeName')"
                    fieldName="type_name"
                    fieldRequired="true"
                />
            </div>

            


            <div class="form-group">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <x-forms.label fieldId="description" :fieldLabel="__('app.description')" />

                    <button type="button"
                            id="ai-generate-desc"
                            class="btn btn-sm btn-outline-primary" style="display: none;">
                        🤖 Generate with AI
                    </button>
                </div>

                <div id="description"></div>
                <textarea name="description" id="description-text" class="d-none"></textarea>
            </div>

            <div class="form-group">
                <x-forms.select
                    fieldId="status"
                    :fieldLabel="__('app.status')"
                    fieldName="status">
                    <option value="yes">@lang('app.active')</option>
                    <option value="no">@lang('app.inactive')</option>
                </x-forms.select>
            </div>

            <x-form-actions>
                <x-forms.button-primary id="save-contractor-type">
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
    $(document).ready(function() {
        quillImageLoad('#description');

        $('#ai-generate-desc').click(function () {

            $.easyAjax({
                url: "{{ route('ai.contractorTypeDesc') }}",
                type: "POST",
                blockUI: true,
                success: function (res) {

                    // Quill editor এ content বসানো
                    $('#description .ql-editor').html(res.text);
                }
            });
        });


        $('#save-contractor-type').click(function () {

            var note = document.getElementById('description').children[0].innerHTML;
            document.getElementById('description-text').value = note;

            const url = "{{ route('contractor-types.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-contractor-type-form',
                type: "POST",
                blockUI: true,
                data: $('#save-contractor-type-form').serialize(),
                success: function (response) {
                    if (response.status === 'success') {
                        window.location.href = "{{ route('contractor-types.index') }}";
                    }
                }
            });
        });
    });

</script>