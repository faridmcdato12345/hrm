@extends('layouts.admin') 
{{-- @section('title') HRM|{{$title}} --}}
 {{-- @endsection --}}
@section('Heading') 
    <h3 class="text-themecolor">Employees</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		
		<li class="breadcrumb-item active">Peoples Management</li>
		<li class="breadcrumb-item active">Payroll</li>
	</ol>
@stop
@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title"> General Payroll</h4>
        <form  class="tab-wizard wizard-circle form py-5" action="{{route('gen.payroll.print')}}" method="post"  enctype="multipart/form-data">
        {{-- <form target="_blank"  class="tab-wizard wizard-circle form py-5" action="{{route('gen.payroll.print')}}" method="post"  enctype="multipart/form-data"> --}}
            {{csrf_field()}}
            <div class="row">
                <div class="col-md-7">
                    <div class="form-group row">
                        <label class="control-label text-left col-md-3">Date From :</label>
                        <div class="col-md-9">
                            <input type="date" class="form-control " id="" placeholder="" name="datefrom"  value="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-7">
                    <div class="form-group row">
                        <label class="control-label text-left col-md-3">Date To :</label>
                        <div class="col-md-9">
                            <input type="date" class="form-control" id="" placeholder="" name="dateto"  value="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-7">
                    <div class="form-group row">
                        @php
                            $dep = DB::connection('pgsql_external')->table('personnel_department')->get();
                        @endphp
                        <label class="control-label text-left col-md-3">Department :</label>
                        <div class="col-md-9">
                            <select class="form-control custom-select" name="designation_id">
                                @foreach ($dep as $department)
                                <option value="{{$department->id}}" @if($department->id == 1) selected @endif>{{$department->dept_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9 py-5">
                <button class="btn btn-success" id="" type="submit"> Generate Payroll</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
$(document).ready(function() {
    $('#myTable').DataTable({
        stateSave: true,
    });
});
</script>
@endpush
@stop