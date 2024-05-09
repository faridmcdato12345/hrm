@extends('layouts.admin') @section('title') HRM|{{$title}} @endsection
@section('Heading') 
    <h3 class="text-themecolor">Employees</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		
		<li class="breadcrumb-item active">Employees</li>
	</ol>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="card-title float-left"> {{$employees->count()}} Employees</h4>
            <a class="btn btn-success btn-sm float-right" href="{{ route('export.leave.form') }}">Print Leave Form</a>
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
                            <a class="btn btn-success btn-sm" href="{{route('employeeaddleaves',['id'=>$employee->id])}}">Add Leave</a>
                            <a class="btn btn-info btn-sm" href="{{route('employee.leave.show',['id'=>$employee->id])}}">Show Leave Details</a>
                            <a class="btn btn-success btn-sm" id="earnedLeaveButton" data-employee-id="{{$employee->id}}">Earned Leave</a>
                            {{-- <a class="btn btn-success btn-sm" href="{{ route('export.leave.earned',['id'=>$employee->id]) }}">Earned Leave</a> --}}
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
    <!-- Modal HTML -->
<div class="modal fade" id="earnedLeaveModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <!-- Modal content -->
    <div class="modal-dialog">
        <div class="modal-content">
            {{-- <form action="" method="post"> --}}
            <form action="{{ route('export.leave.earned') }}" method="post">
                {{ csrf_field() }}
                <div class="modal-header">
                    Earned Leave
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">Select Year</label>
                        {{-- <input type="month" name="montYear"  class="form-control"> --}}
                        <input type="number" name="montYear" placeholder="YYYY" class="form-control" min="1900" max="2099" step="1">
                        <input type="hidden" name="employee_id" id="employeeId">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" >Cancel</button>
                    <button type="submit" class="btn btn-info btn-ok">Submit</button>
                </div>
            </form>
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
    // jQuery event handler for the earnedLeaveButton click
    $(document).on('click','#earnedLeaveButton',function() {
        var employeeId = $(this).data('employee-id'); // Retrieve the employee ID from the data attribute
        $('#employeeId').val(employeeId); // Set the employee ID in the hidden input field
        $('#earnedLeaveModal').modal('show'); // Show the modal
    });

    $(document).on('click', '.btn-ok', function() {
        var form = $('#earnedLeaveModal form'); // Get the form element inside the modal
        form.submit(); // Submit the form
    });

</script>
@endpush
@stop
