<div class="bg-white rounded b-shadow-4 create-inv">

    <div class="px-3 py-3 px-lg-4 px-md-4">
        <h4 class="mb-0 f-21 font-weight-normal">
            Edit Requisition
        </h4>
    </div>

    <hr class="m-0 border-top-grey">

    <x-form class="c-inv-form" id="update-requisition-form">
        @method('PUT')

        <div class="row px-lg-4 px-md-4 px-3 py-3">

            <div class="col-md-3">
                <label>Requisition No</label>
                <input type="text" class="form-control height-35"
                       name="req_no" value="{{ $requisition->req_no }}" readonly>
            </div>

            <div class="col-md-3 r-delivery">
			    <x-forms.datepicker
			        fieldId="delivery_date"
			        fieldLabel="Delivery Date"
			        fieldName="delivery_date"
			        fieldPlaceholder="Select Date"
			        :fieldValue="\Carbon\Carbon::parse($requisition->delivery_date)->format(company()->date_format)"
			    />
			</div>

            <div class="col-md-3">
                <label>Project</label>
                <select class="form-control" name="project_id" required>
                    <option value="">Select Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}"
                            {{ $project->id == $requisition->project_id ? 'selected' : '' }}>
                            {{ $project->project_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label>Delivery Place</label>
                <input type="text" class="form-control height-35"
                       name="delivery_place"
                       value="{{ $requisition->delivery_place }}">
            </div>

            <div class="col-md-12 mt-3">
                <label>Note</label>
                <textarea class="form-control" name="note" rows="3">{{ $requisition->note }}</textarea>
            </div>
        </div>

        <hr>

        <div class="row mx-3">
            <div class="col-md-12">
                <h5>Items</h5>

                <div id="items-list">
                    @foreach($requisition->items as $i => $item)
                        <div class="item-card">
                            <div class="item-handle d-flex justify-content-between">
                                <strong>Item</strong>
                                <button type="button" class="btn btn-sm btn-danger remove-item">Remove</button>
                            </div>

                            <div class="row p-2">
                                <div class="col-md-6">
                                    <label>Item Name</label>
                                    <input type="text" class="form-control"
                                           name="items[{{ $i }}][item_name]"
                                           value="{{ $item->item_name }}" required>
                                </div>

                                <div class="col-md-3">
                                    <label>Quantity</label>
                                    <input type="number" class="form-control"
                                           name="items[{{ $i }}][quantity]"
                                           value="{{ $item->quantity }}" required>
                                </div>

                                <div class="col-md-3">
                                    <label>Unit</label>
                                    <input type="text" class="form-control"
                                           name="items[{{ $i }}][unit]"
                                           value="{{ $item->unit }}">
                                </div>

                                <input type="hidden"
                                       name="items[{{ $i }}][position]"
                                       value="{{ $i }}">
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" id="add-item" class="btn btn-sm btn-secondary mt-2">
                    + Add Item
                </button>
            </div>
        </div>

        <x-form-actions class="mt-4">
            <button type="button" class="btn btn-primary save-update">
                Update
            </button>
        </x-form-actions>
    </x-form>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
$(document).ready(function () {

	datepicker('#delivery_date', {
                position: 'bl',
                ...datepickerConfig
            });



    let $itemsList = $('#items-list');


    function toggleRemoveButtons() {
        let total = $itemsList.children('.item-card').length;

        if (total === 1) {
            $('.remove-item').prop('disabled', true);
        } else {
            $('.remove-item').prop('disabled', false);
        }
    }
    // ---------- ADD ITEM ----------
    function addItemRow() {
        let index = $itemsList.children('.item-card').length;

        let row = `
        <div class="item-card">
            <div class="item-handle d-flex justify-content-between align-items-center">
                <strong>Item</strong>
                <button type="button" class="btn btn-sm btn-danger remove-item">Remove</button>
            </div>

            <div class="row p-2">
                <div class="col-md-6">
                    <label>Item Name</label>
                    <input type="text" class="form-control"
                           name="items[${index}][item_name]" required>
                </div>

                <div class="col-md-3">
                    <label>Quantity</label>
                    <input type="number" class="form-control"
                           name="items[${index}][quantity]" value="1" min="1" required>
                </div>

                <div class="col-md-3">
                    <label>Unit</label>
                    <input type="text" class="form-control"
                           name="items[${index}][unit]">
                </div>

                <input type="hidden"
                       name="items[${index}][position]"
                       class="item-position"
                       value="${index}">
            </div>
        </div>`;

        $itemsList.append(row);
        reIndexItems();
        toggleRemoveButtons();
    }

    // ---------- REINDEX ----------
    function reIndexItems() {
        $itemsList.find('.item-card').each(function (i) {

            $(this).find('input').each(function () {
                let name = $(this).attr('name');
                if (name) {
                    $(this).attr('name', name.replace(/items\[\d+\]/, 'items[' + i + ']'));
                }
            });

            $(this).find('.item-position').val(i);
        });
    }

    // ---------- REMOVE ----------
    $(document).on('click', '.remove-item', function () {
        if ($itemsList.children('.item-card').length > 1) {
            $(this).closest('.item-card').remove();
            reIndexItems();
            toggleRemoveButtons();
        }
    });

    // ---------- SORTABLE ----------
    new Sortable($itemsList[0], {
        handle: '.item-handle',
        animation: 150,
        onEnd: function () {
            reIndexItems();
        }
    });

    // ---------- ADD BUTTON ----------
    $('#add-item').on('click', function () {
        addItemRow();
    });

    toggleRemoveButtons();
    // ---------- UPDATE SUBMIT ----------
    $('.save-update').on('click', function (e) {
        e.preventDefault();

        let url = "{{ route('requisitions.update', $requisition->id) }}";
        let form = $('#update-requisition-form')[0];
        let formData = new FormData(form);

        $.easyAjax({
            url: url,
            container: '#update-requisition-form',
            type: "POST",
            file: true,
            data: formData,
            blockUI: true,
            success: function (response) {
                if (response.status === 'success') {
                    window.location.href = "{{ route('requisitions.index') }}";
                }
            }
        });
    });

});
</script>
@endpush

