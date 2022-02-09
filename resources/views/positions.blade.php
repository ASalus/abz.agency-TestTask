@extends('layouts.app')


@section('content')
<div class="content-wrapper bg-dark
" style="min-height: 327px;">
    <div class="content-header">
        <div class="row mb-2">
            <div class="col-6">
                <h1 class="table-header">Positions</h1>
            </div>
            <div class="col-6">
                <button class="createPosition btn btn-outline-light float-right" data-bs-toggle="modal" data-bs-target="#position-modal" id="createPositionBtn">Add Position</button>
            </div>
        </div>
    </div>
    <div class="content ">{{$dataTable->table()}}</div>
</div>

@include('includes.position-modal')
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
@endpush
