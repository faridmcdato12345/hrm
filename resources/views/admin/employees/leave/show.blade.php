@extends('layouts.admin')
<style>
	button:disabled{
		cursor:not-allowed;
		pointer-events: all !important;
	}
</style>
@section('Heading') 
<button type="button"  onclick="window.location.href='{{route('employeeleaves')}}'" class="btn btn-danger btn-rounded m-t-10 float-right"> Back</button>
	<h3 class="text-themecolor" style="text-transform: capitalize;">{{$employee->firstname}} {{$employee->lastname}} Leaves Record</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		
		<li class="breadcrumb-item active">Employees</li>
		<li class="breadcrumb-item active">Leave</li>
        <li class="breadcrumb-item active">Record</li>
	</ol>
@stop
@section('content')
<div class="card">
    <div class="card-body">
        <div class="table">
            <table id="myTable" class="display nowrap table table-hover table-striped table-bordered" data-paging="true" data-paging-size="7">
                @if(count($leaves) > 0)
                <thead>
                <tr>
                    <th>From</th>
                    <th>To</th>
                    <th>Type</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($leaves as $leave)
                    <tr>
                        <td>{{$leave->datefrom}}</td>
                        <td>{{$leave->dateto}}</td>
                        <td>{{$leave->leaveType->name}}</td>
                        {{-- <td><a target="_blank" href="{{route('employee.appointment.file',['id'=>$appointment->id])}}" class="btn btn-primary">Show file</button></a> --}}
                    </tr>
                    @endforeach
                @else
                <p class="text-center" style="margin-top:70px;" >No Leave/s.</p>
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
