<div class="row">
    <div class="col-sm-12">
        <x-form id="save-steps-form">

            {{-- Project --}}
            <div class="form-group">
                <x-forms.select fieldId="project_id" :fieldLabel="__('modules.contractor.project')" fieldName="project_id" fieldRequired="true">
                    <option value="">@lang('app.select')</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                    @endforeach
                </x-forms.select>
            </div>

            {{-- Section --}}
            <div class="form-group">
                <x-forms.select fieldId="section_id" :fieldLabel="__('modules.contractor.sectionName')" fieldName="section_id" fieldRequired="true">
                    <option value="">@lang('app.select')</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}">{{ $section->section_name }}</option>
                    @endforeach
                </x-forms.select>
            </div>

            {{-- Step Name --}}
            <div class="form-group">
                <x-forms.text fieldId="step_name" :fieldLabel="__('modules.contractor.stepName')" fieldName="step_name" fieldRequired="true" />
            </div>

            {{-- Description --}}
            <div class="form-group">
                <x-forms.label fieldId="step_description" :fieldLabel="__('app.description')" />
                <div id="step_description"></div>
                <textarea name="step_description" id="step-description-text" class="d-none"></textarea>
            </div>

            {{-- Status --}}
            <div class="form-group">
                <x-forms.select fieldId="status" :fieldLabel="__('app.status')" fieldName="status">
                    <option value="0">@lang('modules.contractor.pending')</option>
                    <option value="1">@lang('modules.contractor.on_process')</option>
                    <option value="2">@lang('modules.contractor.finished')</option>
                </x-forms.select>
            </div>

            <x-form-actions>
                <x-forms.button-primary id="save-steps">@lang('app.save')</x-forms.button-primary>
                <x-forms.button-cancel data-dismiss="modal">@lang('app.cancel')</x-forms.button-cancel>
            </x-form-actions>

        </x-form>
    </div>
</div>

<script>
$(document).ready(function() {
    quillImageLoad('#step_description');

    $('#save-steps').click(function() {
        let note = document.getElementById('step_description').children[0].innerHTML;
        document.getElementById('step-description-text').value = note;

        $.easyAjax({
            url: "{{ route('steps.store') }}",
            container: '#save-steps-form',
            type: "POST",
            blockUI: true,
            data: $('#save-steps-form').serialize(),
            success: function(response){
                if(response.status === 'success'){
                    window.location.href = "{{ route('steps.index') }}";
                }
            }
        });
    });
});
</script>
