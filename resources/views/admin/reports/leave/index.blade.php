@extends('layouts.admin') @section('title') HRM @endsection
@section('Heading')
    {{-- <button type="button"  onclick="window.location.href='{{route('salary.create')}}'" class="btn btn-info btn-rounded m-t-10 float-right"><span class="fas fa-plus" ></span> Add Employee</button> --}}
    <h3 class="text-themecolor">Leave Report</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		<li class="breadcrumb-item active">Leave Report</li>
	</ol>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="card-title"> Active / {{$employees->count()}} Employees</h4>
            <div class="table-responsive m-t-40">
                <table id="myTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        @if(count($employees) > 0)
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Department</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($employees as $employee)
                    <tr>
                        <input type="hidden" name="" value="{{$employee->id}}" id="id">
                        <td>{{$employee->firstname}} {{$employee->lastname}}</td>
                        <td>{{ ucfirst(trans($employee->designation))}}</td>
                        <td>{{isset($employee->department) ? $employee->department->department_name : ''}}</td>
                        <td class="text-nowrap">
                            <a class="btn btn-info btn-sm" href="#" data-toggle="modal" data-original-title="Check" data-target="#check{{$employee->id}}">Check Leave Report</a>
                                <div class="modal fade" id="check{{ $employee->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{route('report.leave.output',['id'=>$employee->id])}}" method="post">
                                                {{ csrf_field() }}
                                                <div class="modal-header">
                                                    Year
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label">Enter Year:</label><br>
                                                        <input  type="text" name="year"  placeholder="Enter the year here.." class="form-control">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                    <button  type="submit" class="btn btn-success btn-ok">Check</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <a href="{{route('leave.history',['id'=>$employee->id])}}" class="btn btn-success btn-sm">Leave Earned History</a>
                        </td>
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
    <script type="text/javascript">
        $(document).ready(function () {
            $("#filter").change(function(e){
                if ($(this).val()=== "select" ){
                    var url = "{{route('employees')}}/"
                }
                else{
                    var url = "{{route('employees')}}/" + $(this).val();
                }
                if (url) {
                    window.location = url;
                }
                return false;
            });
        });
    </script>
<script>
    $(document).ready(function() {
        let table = $('#myTable').DataTable({
            stateSave: true,
        });
    });
</script>
@endpush
@stop
