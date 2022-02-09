$(document).on('click', '#createButton', function (e) {
    e.preventDefault();
    $('.modal-title').text('Add New Employee');
    $('#action_button').val('Add');
    $('#action').val('Add');
    $('#createForm')[0].reset();
    $('#editEmp').attr('hidden', true);
    $(".errorWarnings").text("");
    $('#create-modal').modal('show');
    $('#profilePic').attr('src', 'images/placeholder.png');
});


$(document).on('click', '#editButton', function (e) {
    e.preventDefault();
    var id = $(this).attr('data-id');

    $('.modal-title').text('Edit employee\'s data');
    $('#action_button').val('Edit');
    $('#action').val('Edit');
    $('#editEmp').attr('hidden', false);
    $(".errorWarnings").text("");
    $("#image").val(null);

    $.ajax({
        type: "GET",
        url: '/employees/' + id +
            '/edit',
        success: function (data) {
            $('#profilePic').attr('src', 'images/' + data.result.image);
            $('#name').val(data.result.name);
            $('#phone').val(data.result.phone_number);
            $('#email').val(data.result.email);
            $('#salary').val(data.result.salary);
            $('#position_id').val(data.position_name);
            $('#date').val(moment(data.result.employment_date).format('DD.MM.YY'));
            $('#head').val(data.head_name);
            $('#hidden_id').val(data.result.id);
            $('#createdAtDateEmp').text(moment(data.result.created_at).format('DD.MM.YY'));
            $('#updatedAtDateEmp').text(moment(data.result.updated_at).format('DD.MM.YY'));
            $('#createdAdminIdEmp').text(data.result.admin_created_id);
            $('#updatedAdminIdEmp').text(data.result.admin_updated_id);
        }
    })
    $('#create-modal').modal('show');
});

$(document).on('submit', '#createForm', function (e) {
    e.preventDefault();
    let action_url = '';
    if ($('#action').val() == 'Add') {
        action_url = '/employees/submit'
    } else if ($('#action').val() == 'Edit') {
        let id = $('#hidden_id').val()
        action_url = '/employees/' + id + '/update'
    }
    $.ajax({
        type: "POST",
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: action_url,
        data: new FormData($('#createForm')[0]),
        contentType: false,
        processData: false,
        success: function (response) {
            $('#employees-table').DataTable().ajax.reload();
            $('#create-modal').modal('hide');
            $('#resultMessage').text(response.success);
            $('#successMessage').modal('show');
        },
        error: function (response) {
            $(".errorWarnings").text("");
            var errors = response.responseJSON.errors;
            console.log(response.responseJSON.message);
            $.each(errors, function (key, value) {
                console.log(key + " " + value);
                if ($.isPlainObject(value)) {
                    $.each(value, function (key, value) {
                        $('#' + key + 'Error').text('*' + value);
                    });
                } else {
                    $('#' + key + 'Error').text('*' + value); //
                }
            });
        }
    });
});

$(document).on('click', '#deleteEmployee', function (e) {
    e.preventDefault();
    var id = $(this).attr('data-id');
    $('.modal-title').text('Remove employee');
    $.ajax({
        type: "GET",
        url: '/employees/' + id + '/edit',
        dataType: "json",
        success: function (data) {
            $('#delPosName').text('employee ' + data.result.name);
            $('#idHidden').val(data.result.id)
        }
    });
    $('#conformation-modal').modal('show');
});

$(document).on('click', '#submitDelete', function (e) {
    e.preventDefault();
    var id = $('#idHidden').val();
    $.ajax({
        type: "GET",
        url: "/employees/" + id + '/delete',
        success: function (response) {
            if (response.needToChangeHead == true) {
                let id = response.headChange;
                $('#conformation-modal').modal('hide');
                $('.modal-title').text('Edit employee\'s data');
                $('#action_button').val('Edit');
                $('#action').val('Edit');
                $('#editEmp').attr('hidden', false);
                $("#image").val(null);
                $(".errorWarnings").text("");
                $('.modal').css('overflow-y', 'auto');
                $('#headError').text('* Enter the name of new supervisor');
                $('#create-modal').modal('show');
                $.ajax({
                    type: "GET",
                    url: '/employees/' + id + '/edit',
                    dataType: "json",
                    success: function (data) {
                        $('#profilePic').attr('src', 'images/' + data.result.image);
                        $('#name').val(data.result.name);
                        $('#phone').val(data.result.phone_number);
                        $('#email').val(data.result.email);
                        $('#salary').val(data.result.salary);
                        $('#position_id').val(data.position_name);
                        $('#date').val(moment(data.result.employment_date).format('DD.MM.YY'));
                        $('#hidden_id').val(data.result.id);
                        $('#createdAtDateEmp').text(moment(data.result.created_at).format('DD.MM.YY'));
                        $('#updatedAtDateEmp').text(moment(data.result.updated_at).format('DD.MM.YY'));
                        $('#createdAdminIdEmp').text(data.result.admin_created_id);
                        $('#updatedAdminIdEmp').text(data.result.admin_updated_id);
                    }
                });
            } else {
                $('#conformation-modal').modal('hide');
                $('#resultMessage').text(response.message);
                $('#successMessage').modal('show');
            }
        }
    });
    $('#employees-table').DataTable().ajax.reload();
});