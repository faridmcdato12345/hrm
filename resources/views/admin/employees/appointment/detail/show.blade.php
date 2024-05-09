@extends('layouts.admin')
<style>
	button:disabled{
		cursor:not-allowed;
		pointer-events: all !important;
	}
</style>
@section('Heading') 
<button type="button"  onclick="window.location.href='{{route('appointment.index')}}'" class="btn btn-danger btn-rounded m-t-10 float-right"> Back</button>
	<h3 class="text-themecolor" style="text-transform: capitalize;">{{$employees[0]->firstname}} {{$employees[0]->lastname}} Appointments</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		
		<li class="breadcrumb-item active">Employees</li>
		<li class="breadcrumb-item active">Appointment</li>
        <li class="breadcrumb-item active">Details</li>
	</ol>
@stop
@section('content')
<div class="card">
    <div class="card-body">
        <div class="table">
            <table id="myTable" class="display nowrap table table-hover table-striped table-bordered" data-paging="true" data-paging-size="7">
                @if(count($appointments) > 0)
                <thead>
                <tr>
                    <th>From</th>
                    <th>To</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $appointment)
                    <tr>
                        <td>{{$appointment->designationsFrom->designation_name}}</td>
                        <td>{{$appointment->designationsTo->designation_name}}</td>
                        <td>{{$appointment->updated_at}}</td>
                        <td><a target="_blank" href="{{route('employee.appointment.file',['id'=>$appointment->id])}}" class="btn btn-primary">Show file</button></a>
                    </tr>
                    @endforeach
                @else
                <p class="text-center" style="margin-top:70px;" >No appointment/s.</p>
                @endif
                <!---end appointment modal--->
                </tbody>
            </table>
        </div>
    </div>
</div>
@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#myTable').DataTable({
            stateSave: true,
        });        
    });
</script>
@endpush
@stop
