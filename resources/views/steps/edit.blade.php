<div class="row">
    <div class="col-sm-12">
        <x-form id="update-steps-form">

            {{-- Project --}}
            <div class="form-group">
                <x-forms.select 
                    fieldId="project_id" 
                    fieldName="project_id" 
                    :fieldLabel="__('modules.contractor.project')" 
                    fieldRequired="true">
                    <option value="">@lang('app.select')</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" @if($project->id == $step->project_id) selected @endif>
                            {{ $project->project_name }}
                        </option>
                    @endforeach
                </x-forms.select>
            </div>

            {{-- Section --}}
            <div class="form-group">
                <x-forms.select 
                    fieldId="section_id" 
                    fieldName="section_id" 
                    :fieldLabel="__('modules.contractor.section')" 
                    fieldRequired="true">
                    <option value="">@lang('app.select')</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" @if($section->id == $step->section_id) selected @endif>
                            {{ $section->section_name }}
                        </option>
                    @endforeach
                </x-forms.select>
            </div>

            {{-- Step Name --}}
            <div class="form-group">
                <x-forms.text 
                    fieldId="step_name" 
                    fieldName="step_name" 
                    :fieldValue="$step->step_name" 
                    :fieldLabel="__('modules.contractor.stepName')" 
                    fieldRequired="true" />
            </div>

            {{-- Description --}}
            <div class="form-group">
                <x-forms.label fieldId="step_description" :fieldLabel="__('app.description')" />
                <div id="step_description">{!! $step->step_description !!}</div>
                <textarea name="step_description" id="step-description-text" class="d-none"></textarea>
            </div>

            {{-- Status --}}
            <div class="form-group">
                <x-forms.select fieldId="status" fieldName="status" :fieldLabel="__('app.status')">
                    <option value="0" @if($step->status==0) selected @endif>@lang('modules.contractor.pending')</option>
                    <option value="1" @if($step->status==1) selected @endif>@lang('modules.contractor.on_process')</option>
                    <option value="2" @if($step->status==2) selected @endif>@lang('modules.contractor.finished')</option>
                </x-forms.select>
            </div>

            <x-form-actions>
                <x-forms.button-primary id="update-steps">@lang('app.update')</x-forms.button-primary>
                <x-forms.button-cancel onclick="window.history.back()">@lang('app.cancel')</x-forms.button-cancel>
            </x-form-actions>

        </x-form>
    </div>
</div>

<script>
$(document).ready(function(){
    // Quill editor
    quillImageLoad('#step_description');

    // Ajax Update
    $('#update-steps').click(function(){
        let note = document.getElementById('step_description').children[0].innerHTML;
        document.getElementById('step-description-text').value = note;

        $.easyAjax({
            url: "{{ route('steps.update', $step->id) }}",
            container: '#update-steps-form',
            type: "PUT",
            blockUI: true,
            data: $('#update-steps-form').serialize(),
            success: function(response){
                if(response.status==='success'){
                    window.location.href = "{{ route('steps.index') }}";
                }
            }
        });
    });
});
</script>
