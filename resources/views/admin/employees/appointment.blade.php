@extends('layouts.admin') @section('title') HRM|{{$title}} @endsection
@section('Heading') 
    <h3 class="text-themecolor">Employees</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		
		<li class="breadcrumb-item active">Employees</li>
		<li class="breadcrumb-item active">Appointment</li>
	</ol>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="card-title"> {{$employees->count()}} Employees</h4>
            <div class="table-responsive m-t-40">
                <table id="myTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        @if(count($employees) > 0)
						<th>#</th>
                        <th>Photo</th>
						<th>Name</th>
						<th>Action<th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($employees as $employee)
                    <tr>
						<td>{{$employee->emp_code}}</td>
						<td><img width="15%" src="@if(File::exists(public_path('assets/photos/'.$employee->emp_code.'.jpg'))){{asset('assets/photos/'.$employee->emp_code.'.jpg')}} @else {{asset('assets/images/default.png')}} @endif"></td>
                        <td>{{$employee->firstname}} {{$employee->lastname}}</td>
                        @can('EmployeeController:edit')
                        <td class="text-nowrap">
                            <a class="btn btn-success btn-sm" href="{{route('appointment.show',['id'=>$employee->id])}}">Appoint</a>
                            <a class="btn btn-info btn-sm" href="{{route('appointment.detail.show',['id'=>$employee->id])}}">Details</a>
                            <a class="btn btn-secondary btn-sm" href="{{ route('service.record.print',['id'=>$employee->id]) }}">Download Service Record</a>
                        </td>
						<td></td>
                        @endcan
                    </tr>
                    @endforeach @else
                    <tr> No Employee Found</tr>
                    @endif
                    </tbody>
                </table>
            </div>
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
