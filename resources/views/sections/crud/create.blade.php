<div class="row">
    <div class="col-sm-12">
        <x-form id="save-sections-form">

            {{-- Project --}}
            <div class="form-group">
                <x-forms.select
                    fieldId="project_id"
                    :fieldLabel="__('modules.contractor.project')"
                    fieldName="project_id"
                    fieldRequired="true">
                    <option value="">@lang('app.select')</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}">
                            {{ $project->project_name }}
                        </option>
                    @endforeach
                </x-forms.select>
            </div>

            {{-- Section Name --}}
            <div class="form-group">
                <x-forms.text
                    fieldId="section_name"
                    :fieldLabel="__('modules.contractor.sectionName')"
                    fieldName="section_name"
                    fieldRequired="true"
                />
            </div>

            {{-- Description --}}
            <div class="form-group">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <x-forms.label
                        fieldId="section_description"
                        :fieldLabel="__('app.description')" />
                </div>

                <div id="section_description"></div>
                <textarea name="section_description"
                          id="section-description-text"
                          class="d-none"></textarea>
            </div>

            {{-- Status --}}
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
                <x-forms.button-primary id="save-sections">
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

        quillImageLoad('#section_description');

        $('#save-sections').click(function () {

            let note = document
                .getElementById('section_description')
                .children[0].innerHTML;

            document.getElementById('section-description-text').value = note;

            const url = "{{ route('sections.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-sections-form',
                type: "POST",
                blockUI: true,
                data: $('#save-sections-form').serialize(),
                success: function (response) {
                    if (response.status === 'success') {
                        window.location.href = "{{ route('sections.index') }}";
                    }
                }
            });
        });
    });
</script>
