<div class="row">
    <div class="col-sm-12">
        <x-form id="update-sections-form">

            {{-- Project --}}
            <div class="form-group">
                <x-forms.select
                    fieldId="project_id"
                    :fieldLabel="__('modules.contractor.project')"
                    fieldName="project_id"
                    fieldRequired="true">
                    <option value="">@lang('app.select')</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}"
                            @if ($section->project_id == $project->id) selected @endif>
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
                    :fieldValue="$section->section_name"
                />
            </div>

            {{-- Description --}}
            <div class="form-group">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <x-forms.label
                        fieldId="section_description"
                        :fieldLabel="__('app.description')" />
                </div>

                <div id="section_description">
                    {!! $section->section_description !!}
                </div>
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
                    <option value="yes" @if($section->status === 'yes') selected @endif>
                        @lang('app.active')
                    </option>
                    <option value="no" @if($section->status === 'no') selected @endif>
                        @lang('app.inactive')
                    </option>
                </x-forms.select>
            </div>

            <x-form-actions>
                <x-forms.button-primary id="update-sections">
                    @lang('app.update')
                </x-forms.button-primary>

                <x-forms.button-cancel onclick="window.history.back()">
                    @lang('app.cancel')
                </x-forms.button-cancel>
            </x-form-actions>

        </x-form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        quillImageLoad('#section_description');

        $('#update-sections').click(function () {

            let note = document
                .getElementById('section_description')
                .children[0].innerHTML;

            document.getElementById('section-description-text').value = note;

            const url = "{{ route('sections.update', $section->id) }}";

            $.easyAjax({
                url: url,
                container: '#update-sections-form',
                type: "PUT",
                blockUI: true,
                data: $('#update-sections-form').serialize(),
                success: function (response) {
                    if (response.status === 'success') {
                        window.location.href = "{{ route('sections.index') }}";
                    }
                }
            });
        });
    });
</script>
