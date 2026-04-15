<div class="container my-5">
    <div class="row">

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <strong>Add Validation Role</strong>
                </div>

                <div class="card-body">

                    <x-form id="save-validation-role-form">

                        <div class="form-group">
                            <label>Validation Name</label>
                            <input type="text" name="validation_name" id="validation_name" class="form-control height-35 f-14" required>
                        </div>

                        <input type="hidden" name="id" id="validation_role_id">
                        <input type="hidden" name="_method" id="form_method" value="POST">

                    </x-form>

                    <button type="button" id="submit-validation-role" class="btn btn-primary mt-2">Save</button>

                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <strong>Validation Role List</strong>
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
                        @forelse($validationRoles as $role)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $role->validation_name }}</td>
                                <td class="d-flex gap-2">
                                    <a href="javascript:;"
                                       class="btn btn-sm btn-info edit-validation-role"
                                       data-id="{{ $role->id }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    @if($role->non_delatable == 0)
                                    <form method="POST"
                                          action="{{ route('validation-settings.destroy', $role->id) }}"
                                          onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                    </form>
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

<script>
    $('#submit-validation-role').click(function () {

        let id = $('#validation_role_id').val();

        let url = "{{ route('validation-settings.store') }}";

        if (id) {
            url = "{{ url('account/settings/validation-settings') }}/" + id;
            $('#form_method').val('PUT'); // update
        } else {
            $('#form_method').val('POST'); // create
        }

        $.easyAjax({
            url: url,
            container: '#save-validation-role-form',
            type: "POST", // always POST
            disableButton: true,
            buttonSelector: "#submit-validation-role",
            data: $('#save-validation-role-form').serialize(),
            success: function (response) {
                if (response.status === 'success') {
                    window.location.reload();
                }
            }
        });
    });

    $(document).on('click', '.edit-validation-role', function () {

        var id = $(this).data('id');

        $.easyAjax({
            url: "{{ route('validation-settings.index') }}",
            type: "GET",
            data: {
                tab: 'validation-manage',
                id: id
            },
            success: function (res) {

                if (res.status === 'success') {

                    $('#right-panel').html(res.html);

                    // 🔥 HERE IS THE MAGIC
                    if (res.role) {
                        $('#validation_name').val(res.role.validation_name);
                        $('#validation_role_id').val(res.role.id);
                        $('#form_method').val('PUT');
                        $('#submit-validation-role').text('Update');
                    }
                }
            }
        });
    });


</script>


