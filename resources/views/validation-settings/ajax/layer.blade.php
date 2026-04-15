<div class="my-2 p-5">
    <div class="row">

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <strong>Validation Permission</strong>
                </div>

                <div class="card-body">

                <x-form id="save-validation-role-permission-form">

                    <div class="form-group">
                        <label>Validation Name</label>
                        <select name="validation_role_id" id="validation_role_id"
                                class="form-control select-picker" data-live-search="true" required>
                            <option value="">-- Select Validation Name --</option>

                            @foreach($validationRoles as $validationRole)
                                <option value="{{ $validationRole->id }}"
                                        @if(isset($role) && $role->id == $validationRole->id) selected @endif>
                                    {{ $validationRole->validation_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mt-3">
                        <label>Validation Priority Setup</label>
                         <div class="alert alert-danger small mb-2" role="alert" style="font-weight:bold;">
                            ⚠ Employees with lower priority numbers will act first, and the last employee listed will have the highest priority (final stage of validation).
                        </div>

                        <table class="table table-bordered" id="priorityTable">
                            <thead>
                                <tr>
                                    <th width="40">↕</th>
                                    <th>Employee</th>
                                    <th width="120">Priority</th>
                                    <th width="60">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($rolePermissions) && $rolePermissions->count())
                                    @foreach($rolePermissions as $perm)
                                        <tr>
                                            <td class="handle text-center" style="cursor:move">☰</td>

                                            <td>
                                                <select name="employees[]" class="form-control employee-select" required>
                                                    <option value="">-- Select Employee --</option>
                                                    @foreach($employees as $emp)
                                                        <option value="{{ $emp->id }}"
                                                                data-designation="{{ $emp->employeeDetail->designation->id ?? '' }}"
                                                                @if($emp->id == $perm->employee_id) selected @endif>
                                                            {{ $emp->name }}
                                                            ({{ $emp->employeeDetail->designation->name ?? '' }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>

                                            <td>
                                                <input type="text"
                                                       name="priorities[]"
                                                       class="form-control priority text-center"
                                                       value="{{ $perm->priority }}"
                                                       readonly>
                                            </td>

                                            <input type="hidden" name="designation_id[]" class="designation-id"
                                                   value="{{ $perm->designation_id }}">

                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm removeRow">✕</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>

                        <button type="button" id="addRow" class="btn btn-success btn-sm">
                            + Add Row
                        </button>
                    </div>
                    <input type="hidden" name="form_method" id="form_method" value="POST">
                </x-form>

                <button type="button" id="submit-validation-role-permission" class="btn btn-primary mt-2">Save</button>


                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <strong>Validation Permission List</strong>
                </div>

                <div class="card-body p-3">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Validation Name</th>
                                <th width="80">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($validationPermissions as $permission)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $permission->validationRole->validation_name ?? 'N/A' }}</td>
                                    <td class="d-flex gap-2">
                                        <a href="javascript:;"
                                           class="btn btn-sm btn-info edit-validation-role-permission"
                                           data-validation_role_id="{{ $permission->validation_role_id }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        @if($permission->non_delatable == 0)
                                        <button type="button" class="btn btn-sm btn-danger delete-validation-permission" data-validation_role_id="{{ $permission->validation_role_id }}"><i class="fa fa-trash"></i></button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No data found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>

            </div>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
$(document).ready(function () {

    function updatePriority() {
        $('#priorityTable tbody tr').each(function (index) {
            $(this).find('.priority').val(index + 1);
        });
        updateRemoveButtons();
    }
    function updateRemoveButtons() {
        let totalRows = $('#priorityTable tbody tr').length;
        if(totalRows <= 1){
            $('#priorityTable tbody tr .removeRow').prop('disabled', true);
        } else {
            $('#priorityTable tbody tr .removeRow').prop('disabled', false);
        }
    }

    function refreshEmployeeOptions() {
        let selectedEmployees = [];

        $('.employee-select').each(function () {
            let val = $(this).val();
            if (val) selectedEmployees.push(val);
        });

        $('.employee-select').each(function () {
            let currentSelect = $(this);
            let currentVal = currentSelect.val();

            currentSelect.find('option').each(function () {
                let optionVal = $(this).attr('value');
                if (!optionVal) return;

                if (selectedEmployees.includes(optionVal) && optionVal !== currentVal) {
                    $(this).prop('disabled', true);
                } else {
                    $(this).prop('disabled', false);
                }
            });
        });

        $('.employee-select').trigger('change.select2');
    }

    function addRow(empId = '', designationId = '', priority = '') {
        let row = `
        <tr>
            <td class="handle text-center" style="cursor:move">☰</td>
            <td>
                <select name="employees[]" class="form-control employee-select" required>
                    <option value="">-- Select Employee --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}"
                                data-designation="{{ $emp->employeeDetail->designation->id ?? '' }}"
                                ${ empId == {{ $emp->id }} ? 'selected' : '' }>
                            {{ $emp->name }}
                            ({{ $emp->employeeDetail->designation->name ?? '' }})
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="text" name="priorities[]" class="form-control priority text-center" value="${priority}" readonly>
            </td>
            <input type="hidden" name="designation_id[]" class="designation-id" value="${designationId}">
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm removeRow">✕</button>
            </td>
        </tr>
        `;

        $('#priorityTable tbody').append(row);
        $('.employee-select').select2({ width: '100%' });

        updatePriority();
        refreshEmployeeOptions();
    }

    $('#addRow').click(function () { addRow(); });

    $(document).on('click', '.removeRow', function () {
        $(this).closest('tr').remove();
        updatePriority();
        refreshEmployeeOptions();
    });

    $(document).on('change', '.employee-select', function () {
        refreshEmployeeOptions();
        let designationId = $(this).find(':selected').data('designation') || '';
        $(this).closest('tr').find('.designation-id').val(designationId);
    });

    new Sortable(document.querySelector('#priorityTable tbody'), {
        handle: '.handle',
        animation: 150,
        onEnd: function () { updatePriority(); }
    });

    // Submit form
    $('#submit-validation-role-permission').click(function () {
        var method = $('#form_method').val(); // POST or PUT
        var validationRoleId = $('#validation_role_id').val();

        var url = (method === 'PUT') 
            ? "{{ url('validation-settings/update_permission') }}/" + validationRoleId
            : "{{ route('validation-settings.store_permission') }}";

        $.easyAjax({
            url: url,
            container: '#save-validation-role-permission-form',
            type: "POST", // always POST, laravel will handle PUT via hidden _method
            data: $('#save-validation-role-permission-form').serialize(),
            disableButton: true,
            buttonSelector: "#submit-validation-role-permission",
            success: function(response) {
                if(response.status === 'success'){
                    window.location.reload();
                }
            }
        });
    });


    // Edit click
    $(document).on('click', '.edit-validation-role-permission', function () {
        var validation_role_id = $(this).data('validation_role_id');
        
        $.easyAjax({
            url: "{{ route('validation-settings.index') }}",
            type: "GET",
            data: { tab: 'validation-settings', validation_role_id: validation_role_id },
            success: function(res) {
                if(res.status === 'success') {
                   
                    $('#right-panel').html(res.html);
                    $('#priorityTable tbody').empty();
                    if(res.rolePermissions && res.rolePermissions.length) {
                        res.rolePermissions.forEach(function(p){
                            addRow(p.employee_id, p.designation_id, p.priority);
                        });
                        $('#validation_role_id').val(res.role.id).trigger('change');
                        $('#submit-validation-role-permission').text('Update');
                    }
                }
            }
        });
    });

    $(document).on('click', '.delete-validation-permission', function () {
        var validationRoleId = $(this).data('validation_role_id');

        Swal.fire({
            title: 'Are you sure?',
            text: "This will delete all permissions for this role!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.easyAjax({
                    url: "{{ route('validation-settings.destroy_permission', '') }}/" + validationRoleId,
                    type: "DELETE",
                    data: {_token: '{{ csrf_token() }}'},
                    success: function(response) {
                        if (response.status === 'success') {
                            window.location.reload();
                        }
                    }
                });
            }
        });
    });




    // First row auto add
    if($('#priorityTable tbody tr').length == 0) addRow();
    updateRemoveButtons();
});  
</script>





