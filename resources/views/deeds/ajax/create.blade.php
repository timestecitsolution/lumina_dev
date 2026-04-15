<div class="row">
<div class="col-sm-12">

<x-form id="save-deed-form" enctype="multipart/form-data">

{{-- Basic Info --}}
<div class="form-group">
    <x-forms.text fieldId="deed_name" fieldLabel="Deed Name" fieldName="deed_name" fieldRequired="true"/>
</div>

<div class="form-group">
    <x-forms.select fieldId="project_id" fieldLabel="Project" fieldName="project_id" fieldRequired="true">
        <option value="">Select Project</option>
        @foreach($projects as $project)
            <option value="{{ $project->id }}">{{ $project->project_name }}</option>
        @endforeach
    </x-forms.select>
</div>

<div class="form-group">
    <x-forms.select fieldId="contractor_id" fieldLabel="Contractor" fieldName="contractor_id" fieldRequired="true">
        <option value="">Select Contractor</option>
        @foreach($contractors as $contractor)
            <option value="{{ $contractor->id }}">{{ $contractor->name }}
                @if($contractor->type) 
                    ({{ $contractor->type->type_name }})
                @endif
            </option>
        @endforeach
    </x-forms.select>
</div>



<div class="form-group">
    <x-forms.datepicker fieldId="deed_date" :fieldLabel="__('modules.contractor.deed_date')"
                            fieldName="deed_date" :fieldPlaceholder="__('placeholders.date')"
                            :fieldValue="now()->timezone(company()->timezone)->format(company()->date_format)" />
</div>


<div class="form-group">
    <x-forms.file fieldId="deed_file" :fieldLabel="__('modules.contractor.file')" fieldName="deed_file" />
</div>


<hr>

{{-- Sections Wrapper --}}
<div id="section-wrapper"></div>

<button type="button" class="btn btn-primary btn-sm mb-3" id="add-section-row">+ Add Section</button>

<h4 class="text-right mt-3">Grand Total: <span id="grand-total">0</span></h4>

<x-form-actions>
    <x-forms.button-primary id="save-deed">Save</x-forms.button-primary>
</x-form-actions>

</x-form>
</div>
</div>

<script>

$(document).ready(function () {

    datepicker('#deed_date', {
        position: 'bl',
        ...datepickerConfig
    });

    // Initial section load
    if ($('#project_id').val()) {
        addSectionRow(); // first section
    }

    toggleSaveButton();
    recheckSectionButton();
    recheckStepButton();
});

let sectionIndex = 0;
let projectSections = {};
let stepsCache = {};

// Route templates
let routeSectionsTemplate = '{{ route("deed.getSections", ":id") }}';
let routeStepsTemplate = '{{ route("deed.getSteps", ":id") }}';

/* =====================================================
   COMMON HELPERS
===================================================== */

function getUsedSectionIds() {
    let used = [];
    $('.section-block').each(function () {
        let val = $(this).find('.section-select').val() || ''; // value খালি হলেও নাও
        used.push(val.toString());
    });
    return used;
}

function updateGrandTotal() {
    let total = 0;
    $('.section-amount').each(function () {
        total += parseFloat($(this).val()) || 0;
    });
    $('#grand-total').text(total.toFixed(2));
}

function toggleRemoveButtons() {
    let sections = $('.section-block');
    sections.find('.remove-section')
        .prop('disabled', sections.length <= 1);

    sections.each(function () {
        let steps = $(this).find('.step-row');
        steps.find('.remove-step')
            .prop('disabled', steps.length <= 1);
    });
}

function toggleSaveButton() {
    let sectionCount = $('.section-block').length;
    let stepCount = $('.step-row').length;

    $('#save-deed').prop('disabled', !(sectionCount && stepCount));
}

/* =====================================================
   BUTTON CONTROL
===================================================== */

function recheckSectionButton() {
    let projectId = $('#project_id').val();
    if (!projectId || !projectSections[projectId]) {
        $('#add-section-row').prop('disabled', true); // disable if no project
        return;
    }

    let total = projectSections[projectId].length;
    let used = getUsedSectionIds().length;

    $('#add-section-row').prop('disabled', used >= total);
}

function recheckStepButton(sectionBlock) {
    let secId = sectionBlock.find('.section-select').val();
    if (!secId || !stepsCache[secId]) {
        sectionBlock.find('.add-step').prop('disabled', true);
        return;
    }

    let totalSteps = stepsCache[secId].length;
    let currentRows = sectionBlock.find('.step-row').length;

    sectionBlock.find('.add-step')
        .prop('disabled', currentRows >= totalSteps);
}

/* =====================================================
   REFRESH SECTION DROPDOWN
===================================================== */

function refreshAllSectionDropdowns() {
    let projectId = $('#project_id').val();
    if (!projectId || !projectSections[projectId]) return;

    let usedIds = getUsedSectionIds();

    $('.section-select').each(function () {
        let currentVal = $(this).val();
        let select = $(this);

        select.html('<option value="">Select Section</option>');

        projectSections[projectId].forEach(sec => {
            if (!usedIds.includes(sec.id.toString()) ||
                sec.id.toString() === currentVal) {

                select.append(`<option value="${sec.id}">
                    ${sec.section_name}
                </option>`);
            }
        });

        select.val(currentVal);
    });

    recheckSectionButton();
}

/* =====================================================
   ADD SECTION
===================================================== */

function addSectionRow() {
    let projectId = $('#project_id').val();
    if (!projectId) {
        alert('Please select a project first!');
        return;
    }

    let totalSections = projectSections[projectId] ? projectSections[projectId].length : 0;
    let usedSections = getUsedSectionIds().length;
    
    console.log(usedSections);
    console.log(totalSections);
    if (usedSections >= totalSections) {
        alert('You cannot add more sections than available in this project.');
        return;
    }

    let html = `
    <div class="card p-3 mb-3 section-block">
        <div class="d-flex justify-content-between">
            <h6>Section</h6>
            <button type="button"
                class="btn btn-sm btn-danger remove-section">X</button>
        </div>

        <div class="row mt-2">

            <div class="col-md-3">
                <select name="sections[${sectionIndex}][section_id]"
                    class="form-control section-select"
                    data-index="${sectionIndex}" required>
                    <option value="">Select Section</option>
                </select>
            </div>

            <div class="col-md-2">
                <input type="text"
                    name="sections[${sectionIndex}][unit_type]"
                    class="form-control"
                    placeholder="Unit Type" required>
            </div>

            <div class="col-md-2">
                <input type="number"
                    name="sections[${sectionIndex}][per_unit_rate]"
                    class="form-control per-unit-rate"
                    data-index="${sectionIndex}"
                    placeholder="Rate" required>
            </div>

            <div class="col-md-2">
                <input type="number"
                    name="sections[${sectionIndex}][total_unit]"
                    class="form-control total-unit"
                    data-index="${sectionIndex}"
                    placeholder="Total Unit" required>
            </div>

            <div class="col-md-3">
                <input type="number"
                    readonly
                    class="form-control section-amount"
                    name="sections[${sectionIndex}][section_amount]"
                    placeholder="Amount">
            </div>
        </div>

        <div class="mt-3">
            <table class="table table-bordered step-table">
                <thead>
                    <tr>
                        <th>Step Name</th>
                        <th>Percentage</th>
                        <th>Budget</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <button type="button"
                class="btn btn-sm btn-info add-step"
                data-index="${sectionIndex}" disabled>
                + Add Step
            </button>
        </div>
    </div>`;

    $('#section-wrapper').append(html);

    let sectionBlock = $('.section-block:last');
    let sectionSelect = sectionBlock.find('.section-select');

    if (projectSections[projectId]) {
        loadSectionOptions(sectionSelect, projectId);
    } else {
        let url = routeSectionsTemplate.replace(':id', projectId);
        $.get(url, function (res) {
            projectSections[projectId] = res.sections;
            loadSectionOptions(sectionSelect, projectId);
        });
    }

    sectionIndex++;
    toggleRemoveButtons();
    recheckSectionButton();
    toggleSaveButton();
}

function loadSectionOptions(select, projectId) {
    let usedIds = getUsedSectionIds();
    select.html('<option value="">Select Section</option>');

    projectSections[projectId].forEach(sec => {
        if (!usedIds.includes(sec.id.toString())) {
            select.append(`<option value="${sec.id}">
                ${sec.section_name}
            </option>`);
        }
    });
}

/* =====================================================
   EVENTS
===================================================== */

$('#add-section-row').click(function () {
    
    addSectionRow();
});

$('#project_id').change(function () {

    let projectId = $(this).val();
    if (!projectId) return;

    let url = routeSectionsTemplate.replace(':id', projectId);

    $.get(url, function (res) {
        projectSections[projectId] = res.sections;
        addSectionRow();
        refreshAllSectionDropdowns();
    });
});


$(document).on('change', '.step-select', function () {
    refreshAllStepDropdowns();
});
$(document).on('change', '.section-select', function () {

    let sectionBlock = $(this).closest('.section-block');
    let container = sectionBlock.find('.step-table');
    let secIndex = $(this).data('index');
    let secId = $(this).val();

    container.find('tbody').html('');

    refreshAllSectionDropdowns();

    if (!secId) {
        recheckStepButton(sectionBlock);
        toggleSaveButton();
        return;
    }

    if (stepsCache[secId]) {
        addStepRow(container, secIndex, stepsCache[secId]);
    } else {
        let url = routeStepsTemplate.replace(':id', secId);
        $.get(url, function (res) {
            stepsCache[secId] = res.steps;
            addStepRow(container, secIndex, res.steps);
        });
    }
});

$(document).on('click', '.remove-section', function () {
    $(this).closest('.section-block').remove();

    refreshAllSectionDropdowns();
    toggleRemoveButtons();
    updateGrandTotal();
    recheckSectionButton();
    toggleSaveButton();
});

$(document).on('click', '.add-step', function () {
    let sectionBlock = $(this).closest('.section-block');
    let container = sectionBlock.find('.step-table');
    let secIndex = $(this).data('index');
    let secId = sectionBlock.find('.section-select').val();

    if (!secId) return;

    addStepRow(container, secIndex, stepsCache[secId]);
});

$(document).on('click', '.remove-step', function () {
    let sectionBlock = $(this).closest('.section-block');

    $(this).closest('.step-row').remove();

    toggleRemoveButtons();
    recheckStepButton(sectionBlock);
    toggleSaveButton();
});

/* =====================================================
   ADD STEP ROW
===================================================== */
function refreshAllStepDropdowns() {
    $('.section-block').each(function () {
        let sectionBlock = $(this);
        let secId = sectionBlock.find('.section-select').val();
        if (!secId || !stepsCache[secId]) return;

        let allUsedSteps = [];

        // Collect all steps used in this section
        sectionBlock.find('.step-row .step-select').each(function () {
            let val = $(this).val();
            if (val) allUsedSteps.push(val.toString());
        });

        // Refresh each step-select in this section block
        sectionBlock.find('.step-row .step-select').each(function () {
            let currentVal = $(this).val();
            let select = $(this);
            select.html('<option value="">Select Step</option>');

            stepsCache[secId].forEach(step => {
                // Show step if not used or if it's the current value
                if (!allUsedSteps.includes(step.id.toString()) || step.id.toString() === currentVal) {
                    select.append(`<option value="${step.id}">${step.step_name}</option>`);
                }
            });

            select.val(currentVal);
        });

        recheckStepButton(sectionBlock);
    });
}


function addStepRow(container, secIndex, steps) {

    let used = container.find('.step-select')
        .map((i, el) => $(el).val())
        .get();
    

    let options = '<option value="">Select Step</option>';

    steps.forEach(step => {
        if (!used.includes(step.id.toString())) {
            options += `<option value="${step.id}">${step.step_name}</option>`;
        }
    });

    let row = `
    <tr class="step-row">
        <td>
            <select class="form-control step-select"
                name="sections[${secIndex}][steps][]" required>
                ${options}
            </select>
        </td>
        <td>
            <input type="number"
                class="form-control step-percentage"
                placeholder="%"
                name="sections[${secIndex}][step_percentage][]" required>
        </td>
        <td>
            <input type="number"
                class="form-control step-budget"
                readonly required>
        </td>
        <td>
            <button type="button"
                class="btn btn-sm btn-danger remove-step">X</button>
        </td>
    </tr>`;

    container.find('tbody').append(row);

    recheckStepButton(container.closest('.section-block'));
    toggleRemoveButtons();
    toggleSaveButton();
}

/* =====================================================
   CALCULATION
===================================================== */

$(document).on('input', '.per-unit-rate, .total-unit', function () {

    let index = $(this).data('index');

    let rate = parseFloat($(`input[name="sections[${index}][per_unit_rate]"]`).val()) || 0;
    let unit = parseFloat($(`input[name="sections[${index}][total_unit]"]`).val()) || 0;

    let amount = rate * unit;

    $(`input[name="sections[${index}][section_amount]"]`)
        .val(amount.toFixed(2));

    $(this).closest('.section-block')
        .find('.step-row')
        .each(function () {

            let perc = parseFloat($(this).find('.step-percentage').val()) || 0;
            let budget = (perc / 100) * amount;

            $(this).find('.step-budget')
                .val(budget.toFixed(2));
        });

    updateGrandTotal();
});

$(document).on('input', '.step-percentage', function () {

    let sectionBlock = $(this).closest('.section-block');
    let sectionAmount = parseFloat(sectionBlock.find('.section-amount').val()) || 0;
    let currentPerc = parseFloat($(this).val()) || 0;

    let total = 0;

    sectionBlock.find('.step-percentage').not(this).each(function () {
        total += parseFloat($(this).val()) || 0;
    });

    if (total + currentPerc > 100) {
        alert('Total percentage cannot exceed 100%');
        $(this).val('');
        return;
    }

    let budget = (currentPerc / 100) * sectionAmount;

    $(this).closest('.step-row')
        .find('.step-budget')
        .val(budget.toFixed(2));
});

/* =====================================================
   SAVE
===================================================== */

$('#save-deed').click(function () {

    if ($(this).prop('disabled')) return;

    let form = document.getElementById('save-deed-form'); 

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    $.easyAjax({
        url: "{{ route('deeds.store') }}",
        container: '#save-deed-form',
        type: "POST",
        blockUI: true,
        file: true,
        success: function (response) {
            if (response.status === 'success') {
                window.location.href = "{{ route('deeds.index') }}";
            }
        }
    });

});

</script>


