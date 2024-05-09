@extends('layouts.admin')
<style>
	button:disabled{
		cursor:not-allowed;
		pointer-events: all !important;
	}
</style>
@section('Heading') 
<button type="button"  onclick="window.location.href='{{route('appointment.index')}}'" class="btn btn-danger btn-rounded m-t-10 float-right"> Back</button>
	<h3 class="text-themecolor" style="text-transform: capitalize;">{{$employees[0]->firstname}} {{$employees[0]->lastname}}</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		
		<li class="breadcrumb-item active">Employees</li>
		<li class="breadcrumb-item active">Appointment</li>
	</ol>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
			<div class="row">
				<div class="col-md-3" style="text-align:center">
					<img width="100%" src="@if(File::exists(public_path('assets/photos/'.$employees[0]->emp_code.'.jpg'))){{asset('assets/photos/'.$employees[0]->emp_code.'.jpg')}} @else {{asset('assets/images/default.png')}} @endif">
				</div>
				<div class="col-md-9">
					<form action="{{route('appointment.store')}}" method="post" enctype="multipart/form-data">
						{{ csrf_field() }}
						<div class="form-group">
							<label for="name">Name:</label>
							<h3>{{$employees[0]->firstname}}&nbsp;{{$employees[0]->lastname}}</h3>
							<input type="hidden" name="employee_id" value="{{$employees[0]->id}}">
						</div>
						<div class="form-group">
							<label for="from_designation">From Designation:</label>
							<input readonly type="text" value="@if($employees[0]->designations) {{$employees[0]->designations->designation_name}} @else Employee Job designation not yet assigned. @endif" class="form-control">
							<input type="hidden" value="@if($employees[0]->designations){{$employees[0]->designations->id}}@else 0 @endif" name="from_designation_id">
						</div>
						<div class="form-group">
							<label for="from_designation">To Designation:</label>
							<select name="to_designation_id" id="designation" class="form-control">
							@forelse($designations as $designation)
								<option value="{{$designation->id}}">{{$designation->designation_name}}</option>
							@empty
								<option>No designation found</option>
							@endforelse
							</select>
						</div>
						<div class="form-group">
							<label for="salary">Salary:</label>
								<input type="text"  name="salary" value="" class="form-control" placeholder="Enter Salary" required>
						</div>
						<div class="form-group">
							<label class="control-label">Date</label>
							<input  type="date" name="created_at" class="form-control">
						</div>
						<div class="form-group">
							<label for="input-file-now">Attatch File </label>
							<br>
							<input type="file" accept="application/pdf" class="form-control" name="document"/>
						</div>
						<div class="form-group">
							@if($employees[0]->designations)
							<input type="submit" value="Appoint" class="btn btn-primary form-control" style="color:#fff;">
							@else
							<button disabled class="btn btn-danger form-control" style="color:#fff;">Appoint</button>
							@endif
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
