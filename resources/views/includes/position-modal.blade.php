<div class="modal fade" id="position-modal" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h1 class="modal-title" id="modal-title">New Employee</h1>
            </div>
            <div class="modal-body">
                <form action="{{ route('position-add') }}" method="post" enctype="multipart/form-data" id="position-Form">
                    @csrf
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="positionName" name="positionName" placeholder="Enter employee's name">
                        <label for="positionName" class="form-label text-dark">Position name:</label>
                        <small class="errorWarnings fst-italic text-warning" id="positionNameError"></small>
                    </div>
                    <div id="editInfo" hidden="true">
                        <div class="row">
                            <div class="col-6">
                                <p class="fw-bold">Created at: <span class="fw-light" id="createdAtDate"></span></p>
                            </div>
                            <div class="col-6">
                                <p class="fw-bold text-end">Admin created ID: <span class="fw-light" id="createdAdminId"></span></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <p class="fw-bold">Updated at: <span class="fw-light" id="updatedAtDate"></span></p>
                            </div>
                            <div class="col-6">
                                <p class="fw-bold text-end">Admin updated ID: <span class="fw-light" id="updatedAdminId"></span></p>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="hidden" name="action" id="action" value="Add">
                <input type="hidden" name="hiddenPosId" id="hiddenPosId" />
                <input type="submit" name="positionActionButton" id="positionActionButton" class="btn btn-primary" value="Add">
            </div>
            </form>
        </div>
    </div>
</div>


@push('scripts')
<script>
    $('#createPositionBtn').click(function(e) {
        e.preventDefault();
        $('.modal-title').text('Add New Position');
        $('#positionActionButton').val('Add');
        $('#action').val('Add');
        $('#position-Form')[0].reset();
        $('#editInfo').attr('hidden', true);
        $(".errorWarnings").text("");
        $('#position-modal').modal('show');
    });

    $(document).on('click', '#editPositionBtn', function(e) {
        e.preventDefault();
        var id = $(this).attr('positionId');
        $('.modal-title').text('Edit Position');
        $('#positionActionButton').val('Edit');
        $('#action').val('Edit');
        $('#editInfo').attr('hidden', false);
        $(".errorWarnings").text("");
        $.ajax({
            type: "GET"
            , url: '/positions/' + id + '/edit'
            , dataType: "json"
            , success: function(data) {
                $('#positionName').val(data.result.position_name);
                $('#hiddenPosId').val(data.result.id);
                $('#createdAtDate').text(moment(data.result.created_at).format('L'));
                $('#updatedAtDate').text(moment(data.result.updated_at).format('L'));
                $('#createdAdminId').text(data.result.admin_created_id);
                $('#updatedAdminId').text(data.result.admin_updated_id);
            }
        });
        $('#position-modal').modal('show');
    })

    $(document).on('submit', '#position-Form', function(e) {
        e.preventDefault();
        let action_url = '';

        if ($('#action').val() == 'Add') {
            action_url = '/positions/submit'
        } else if ($('#action').val() == 'Edit') {
            let id = $('#hiddenPosId').val()
            action_url = '/positions/' + id + '/update'
        }

        let editFormData = new FormData($('#position-Form')[0]);
        $.ajax({
            type: "POST"
            , headers: {
                'X-Requested-With': 'XMLHttpRequest'
                , 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , url: action_url
            , data: new FormData($('#position-Form')[0])
            , contentType: false
            , processData: false
            , success: function(response) {
                $('#position-table').DataTable().ajax.reload();
                $('#position-modal').modal('hide');
                $('#resultMessage').text(response.success);
                $('#successMessage').modal('show');
            }
            , error: function(response) {
                $('#form_result').val('');
                var errors = response.responseJSON.errors;
                //console.log(errors);
                $.each(errors, function(key, value) {
                    console.log(key + " " + value);
                    if ($.isPlainObject(value)) {
                        $.each(value, function(key, value) {
                            $('#' + key + 'Error').text('*' + value);
                        });
                    } else {
                        $('#' + key + 'Error').text('*' + value); //
                    }
                });
            }
        });
    });

    $(document).on('click', '#positionDelete', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        $('.modal-title').text('Remove position?');
        $.ajax({
            type: "GET"
            , url: '/positions/' + id + '/edit'
            , dataType: "json"
            , success: function(data) {
                $('#delPosName').text('position ' + data.result.position_name);
                $('#idHidden').val(data.result.id)
            }
        });
        $('#conformation-modal').modal('show');
    });

    $(document).on('click', '#submitDelete', function(e) {
        e.preventDefault();
        var id = $('#idHidden').val();
        console.log(id);
        $.ajax({
            type: "GET"
            , url: "/positions/" + id + '/delete'
            , success: function(response) {
                $('#position-table').DataTable().ajax.reload();
                $('#conformation-modal').modal('hide');
                $('#resultMessage').text(response.message);
                $('#successMessage').modal('show');
            }
        });
    });

</script>
@endpush
