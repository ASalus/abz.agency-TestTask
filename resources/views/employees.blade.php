@extends('layouts.app')


@section('content')
<div class="content-wrapper bg-dark
" style="min-height: 327px;">
    <div class="content-header">
        <div class="row mb-2">
            <div class="col-6">
                <h1 class="table-header">Employees</h1>
            </div>
            <div class="col-6">
                <button class="create btn btn-outline-light float-right" id="createButton">New Employee</button>
            </div>
        </div>
    </div>
    <div class="content ">{{$dataTable->table()}}</div>
</div>

@include('includes.create-employee-modal')
{{-- @include('includes.update-employee-modal') --}}
@endsection


@push('scripts')
{{$dataTable->scripts()}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.1.62/jquery.inputmask.bundle.js"></script>


@if (count($errors) > 0)
<script type="text/javascript">
    $(document).ready(function() {
        $('#create-modal').modal('show');
    });
</script>
@endif

<script type="text/javascript" src="{{ asset('js/modal-edit.js') }}">
</script>



@endpush
