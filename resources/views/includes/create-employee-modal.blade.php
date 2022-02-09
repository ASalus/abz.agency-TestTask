<div class="modal fade" id="create-modal" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h1 class="modal-title" id="modal-title">New Employee</h1>
            </div>
            <div class="modal-body" style="overflow: relative">
                <form action="{{ route('employee-add') }}" method="post" enctype="multipart/form-data" id="createForm">
                    @csrf
                    <div class="form-group">
                        <div class="row">
                            <label for="image text-dark" class="form-label">Photo</label>
                            <div class="col">
                                <img class="profilePic" id="profilePic" src="/images/placeholder.png" alt="/images/placeholder.png">
                            </div>
                            <div class="col">
                                <input type="file" name="image" id="image" class="form-control" enctype="multipart/form-data">
                                <small class="errorWarnings fst-italic text-warning" id="imageError"></small>
                                <small id="passwordHelpInline" class="text-muted">
                                    File format jpg,png up to 5MB, the minimum size of 300x300px
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter employee's name">
                        <label for="name" class="form-label text-dark">Name:</label>
                        <small class="errorWarnings fst-italic text-warning" id="nameError"></small>
                    </div>
                    <div class='form-group mb-3'>
                        <div class="form-floating">
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter employee's phone number">
                            <label for="phone" class="form-label text-dark">Phone:</label>
                            <small class="errorWarnings fst-italic text-warning" id="phoneError"></small>
                        </div>
                        <small id="phoneHint" class="text-muted">
                            <p class="text-end"> Required format: +380 (XX) XXX XX XX</p>
                        </small>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" name="email" id="email" placeholder="Enter employee's email">
                        <label for="email" class="form-label text-dark">Email:</label>
                        <small class="errorWarnings fst-italic text-warning" id="emailError"></small>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" name="position_id" id="position_id" class="form-control autocomplete" placeholder="Enter employee's position">
                        <label for="position_id" class="form-label text-dark">Position:</label>
                        <small class="errorWarnings fst-italic text-warning" id="position_idError"></small>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="salary" name="salary" placeholder="Enter employee's salary">
                        <label for="salary" class="form-label text-dark">Salary, $:</label>
                        <small class="errorWarnings fst-italic text-warning" id="salaryError"></small>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" name="head" id="head" class="form-control autocomplete" placeholder="Enter employee's supervisor">
                        <label for="head" class="form-label text-dark">Head:</label>
                        <small class="errorWarnings fst-italic text-warning" id="headError"></small>
                    </div>
                    <div class="form-floating mb-3 datepicker" id="container">
                        <input type="text" class="form-control" name="date" id="date" autocomplete="nope">
                        <label for="date" class="form-label text-dark">Date of employment:</label>
                        <small class="errorWarnings fst-italic text-warning" id="dateError"></small>
                    </div>
                    <div id="editEmp" hidden="true">
                        <div class="row">
                            <div class="col-6">
                                <p class="fw-bold">Created at: <span class="fw-light" id="createdAtDateEmp"></span></p>
                            </div>
                            <div class="col-6">
                                <p class="fw-bold text-end">Admin created ID: <span class="fw-light" id="createdAdminIdEmp"></span></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <p class="fw-bold">Updated at: <span class="fw-light" id="updatedAtDateEmp"></span></p>
                            </div>
                            <div class="col-6">
                                <p class="fw-bold text-end">Admin updated ID: <span class="fw-light" id="updatedAdminIdEmp"></span></p>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="action" id="action" value="Add">
                <input type="hidden" name="hidden_id" id="hidden_id">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="submit" name="action_button" id="action_button" class="btn btn-primary" value="Add">
            </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const myModalEl = document.getElementById('create-modal');
    myModalEl.addEventListener('shown.bs.modal', (e) => {
        $("#head").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{url('autocomplete')}}"
                    , data: {
                        term: request.term
                    }
                    , dataType: "json"
                    , success: function(data) {
                        var resp = $.map(data, function(obj) {
                            //console.log(obj.name);
                            return obj.name;
                        })
                        response(resp)
                    }
                });
            }
            , minLength: 3
            , appendTo: "#create-modal"
        });

        $("#position_id").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{url('autoc-position')}}"
                    , data: {
                        term: request.term
                    }
                    , dataType: "json"
                    , success: function(data) {
                        var resp = $.map(data, function(obj) {
                            //console.log(obj.position_name);
                            return obj.position_name;
                        })
                        response(resp)
                    }
                });
            }
            , minLength: 3
            , appendTo: "#create-modal"
        });

        image.onchange = evt => {
            const [file] = image.files
            if (file) {
                profilePic.src = URL.createObjectURL(file)
            }
        }

        var phones = [{
            "mask": "+380 (##) ### ## ##"
        }, {
            "mask": "+380 (##) ### ## ##"
        }];
        $('#phone').inputmask({
            mask: phones
            , greedy: false
            , definitions: {
                '#': {
                    validator: "[0-9]"
                    , cardinality: 1
                }
            }
        });
        $('#salary').on('change click keyup input paste', (function(event) {
            $(this).val(function(index, value) {
                return '$' + value.replace(/(?!\.)\D/g, "").replace(/(?<=\..*)\./g, "").replace(/(?<=\.\d\d).*/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            });
        }));
        $('#date').datepicker({
            dateFormat: 'dd.mm.y'
        , })

        $('#create-modal').on('scroll', function(){
            $( "#date" ).datepicker( "hide" );
        })
    });

</script>
@endpush
