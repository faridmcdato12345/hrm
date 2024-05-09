@extends('layouts.admin')
<style>
	button:disabled{
		cursor:not-allowed;
		pointer-events: all !important;
	}
</style>
@section('Heading') 
<button type="button"  onclick="window.location.href='{{route('employeeleaves')}}'" class="btn btn-danger btn-rounded m-t-10 float-right"> Back</button>
	<h3 class="text-themecolor" style="text-transform: capitalize;">{{$employee['firstname']}} {{$employee['lastname']}}</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		
		<li class="breadcrumb-item active">Employee</li>
		<li class="breadcrumb-item active">Leave</li>
	</ol>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
			<div class="row">
				<div class="col-md-3" style="text-align:center">
					<img width="100%" src="@if(File::exists(public_path('assets/photos/'.$employee['emp_code'].'.jpg'))){{asset('assets/photos/'.$employee['emp_code'].'.jpg')}} @else {{asset('assets/images/default.png')}} @endif">
				</div>
				<div class="col-md-9">
					<form action="{{route('employee.leave.store')}}" method="post" enctype="multipart/form-data">
						{{ csrf_field() }}
						<div class="form-group">
							<label for="name">Name:</label>
							<h3>{{$employee['firstname']}}&nbsp;{{$employee['lastname']}}</h3>
							<input type="hidden" name="employee_id" value="{{$employee['id']}}">
						</div>
						<div class="form-group">
							<label for="from_designation">Leave Type:</label>
							<select name="leave_type" id="designation" class="form-control">
							@forelse($leaveTypes as $leaveType)
								<option value="{{$leaveType->id}}">{{$leaveType->name}}</option>
							@empty
								<option>No Leave type found</option>
							@endforelse
							</select>
						</div>
						<div class="form-group">
							<label class="control-label">Date From:</label>
							<input  type="date" name="datefrom" class="form-control">
						</div>
                        <div class="form-group">
							<label class="control-label">Date To:</label>
							<input  type="date" name="dateto" class="form-control">
						</div>
						<div class="form-group">
							<label for="input-file-now">Attatch File </label>
							<br>
							<input type="file" class="form-control" name="document"/>
						</div>
						<div class="form-group">
							<button class="btn btn-primary form-control" style="color:#fff;">Save</button>
						</div>
					</form>
				</div>
			</div>
        </div>
    </div>
@push('scripts')
<script>
    
</script>
@endpush
@stop
