<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/css/select2.min.css') }}" defer="defer">
@push('styles')
<style>
.item-card{
    border:1px solid #ddd;
    margin-bottom:10px;
    background:#fff;
}
.item-handle{
    background:#f7f7f7;
    padding:8px;
    cursor:move;
    border-bottom:1px solid #ddd;
}
</style>
@endpush
<!-- CREATE BILL
     START -->
<div class="bg-white rounded b-shadow-4 create-inv">


    <!-- HEADING START -->
    <div class="px-3 py-3 px-lg-4 px-md-4">
        <h4 class="mb-0 f-21 font-weight-normal ">@lang('purchase::app.requisition.create_requisistion')</h4>
    </div>
    <!-- HEADING END -->
    <hr class="m-0 border-top-grey">
    <!-- FORM START -->
    <x-form class="c-inv-form" id="save-requisition-data-form">
        <div class="row px-lg-4 px-md-4 px-3 py-3">

            <div class="col-md-3">
                <x-forms.label fieldId="req_no" :fieldLabel="__('purchase::app.req_no')" fieldRequired="true"></x-forms.label>
                <input type="test" class="form-control height-35" name="req_no">
                
            </div>

            <div class="col-md-3 r-delivery">
                <x-forms.datepicker fieldId="delivery_date" :fieldLabel="__('purchase::app.requisition.delivery_date')" fieldName="delivery_date" :fieldPlaceholder="__('placeholders.date')" :fieldValue="now()->timezone(company()->timezone)->format(company()->date_format)" />
            </div>
            <div class="col-md-3">
                <x-forms.label fieldId="project_id" :fieldLabel="__('app.project')" fieldRequired="true"></x-forms.label>
                <select class="form-control he select-picker" name="project_id" id="project_id" data-live-search="true" required>
                    <option value="">Select Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">
                            {{ $project->project_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <x-forms.label fieldId="delivery_place" :fieldLabel="__('purchase::app.delivery_place')" fieldRequired="true"></x-forms.label>
                <input type="test" class="form-control height-35" name="delivery_place">
                
            </div>
            

            <div class="col-md-12">
                <x-forms.label fieldId="" class="" :fieldLabel="__('purchase::app.note')">
                    </x-forms.label>
                    <textarea class="form-control" name="note" id="note" rows="4"
                        placeholder="@lang('purchase::app.note')"></textarea>
            </div>
        </div>

        <hr>
        <div class="row mx-3">
            <div class="col-md-12">
                <h5>Items</h5>
                <div id="items-list"></div>
                <button type="button" id="add-item" class="btn btn-sm btn-secondary mb-2">
                    + Add Item
                </button>
            </div>
        </div>


        

        <!-- CANCEL SAVE SEND START -->
        <x-form-actions class="c-inv-btns d-block d-lg-flex d-md-flex">
            <div class="mb-3 d-flex mb-lg-0 mb-md-0">

                <div class="mr-3 inv-action dropup">
                    <button type="button" class="btn btn-primary save-form" data-type="save">
                        <i class="fa fa-save mr-1"></i> @lang('app.save')
                    </button>
                </div>
            </div>


            <x-forms.button-cancel :link="isset($vendorID) ? route('vendors.show', $vendorID).'?tab=bills' : route('bills.index')" class="border-0">@lang('app.cancel')
            </x-forms.button-cancel>

        </x-form-actions>
        <!-- CANCEL SAVE SEND END -->

    </x-form>
    <!-- FORM END -->
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
        $(document).ready(function () {

            let $itemsList = $('#items-list');

            function addItemRow() {

                let index = $itemsList.children('.item-card').length;

                let row = `
                <div class="item-card">
                    <div class="item-handle d-flex justify-content-between align-items-center">
                        <strong>Item</strong>
                        <button type="button" class="btn btn-sm btn-danger remove-item">Remove</button>
                    </div>

                    <div class="p-2 row">
                        <div class="col-md-6 mb-2">
                            <label>Item Name</label>
                            <input type="text" name="items[${index}][item_name]" class="form-control height-35" required>
                        </div>

                        <div class="col-md-3 mb-2">
                            <label>Quantity</label>
                            <input type="number" name="items[${index}][quantity]" value="1" min="1" class="form-control height-35" required>
                        </div>

                        <div class="col-md-3 mb-2">
                            <label>Unit</label>
                            <input type="text" name="items[${index}][unit]" class="form-control height-35">
                        </div>

                        <input type="hidden" name="items[${index}][position]" class="item-position height-35" value="${index}">
                    </div>
                </div>
                `;

                $itemsList.append(row);
                toggleRemoveButtons();
            }

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

                toggleRemoveButtons();
            }

            $(document).on('click', '.remove-item', function () {
                if ($itemsList.children('.item-card').length > 1) {
                    $(this).closest('.item-card').remove();
                    reIndexItems();
                }
            });

            function toggleRemoveButtons() {
                let total = $itemsList.children('.item-card').length;
                $('.remove-item').prop('disabled', false);

                if (total === 1) {
                    $('.remove-item').prop('disabled', true);
                }
            }

            
            new Sortable($itemsList[0], {
                handle: '.item-handle',
                animation: 150,
                onEnd: function () {
                    reIndexItems();
                }
            });

         
            $('#add-item').on('click', function () {
                addItemRow();
            });

            // Init First Row
            addItemRow();
        });


        $(document).ready(function() {

            datepicker('#delivery_date', {
                position: 'bl',
                ...datepickerConfig
            });


            $('.save-form').on('click', function (e) {
                e.preventDefault();
                
                var url = "{{ route('requisitions.store') }}";
                var form = $('#save-requisition-data-form')[0];
                var formData = new FormData(form);

                    if (KTUtil.isMobileDevice()) {
                        $('.desktop-description').remove();
                    } else {
                        $('.mobile-description').remove();
                    }

                   

                    $.easyAjax({
                        url: "{{ route('requisitions.store') }}",
                        container: '#save-requisition-data-form',
                        type: "POST",
                        blockUI: true,
                        redirect: false,   
                        file: true,
                        data: formData,
                        success: function(response) {
                            if (response.status === 'success') {
                                window.location.href = "{{ route('requisitions.index') }}";
                            }
                        }
                    });
            });
            $('.r-delivery, .r-delivery .my-3').removeClass('my-3');
        });

        $.ajax({
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    if (errors.req_no) {
                        toastr.error(errors.req_no[0]);
                    }
                }
            }
        });
</script>
@endpush