@extends('layouts.admin') @section('title') HRM| @endsection
@section('Heading')
    @if (Auth::user()->isAllowed('EmployeeController:create'))
    <button type="button"  onclick="window.location.href=#" class="btn btn-info btn-rounded m-t-10 float-right"><span class="fas fa-plus" ></span> Add Employee</button>
    @endif
    
    <h3 class="text-themecolor">Employees</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		<li class="breadcrumb-item active">People Management</li>
		<li class="breadcrumb-item active">Employees</li>
	</ol>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="float-right">
                <select class="form-control" id="filter">
                    <option value="select">Select Employees</option>
                    {{-- @foreach($filters as $filter)
                    <option value="{{$filter}}" @if($filter==$selectedFilter) selected @endif>{{ucfirst(trans($filter))}}</option>
                    @endforeach --}}
                </select>
            </div>
            <h4 class="card-title"> Active / Employees</h4>
            <div class="table-responsive m-t-40">
                <table id="myTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            {{-- @if(count($employees) > 0) --}}
                            <th>Name</th>
                            <th>Details</th>
                            @if (Auth::user()->isAllowed('EmployeeController:edit'))
                            <th>Actions</th>
                            @endif
                            
                        </tr>
                    </thead>
                    <tbody>
                    {{-- @foreach($employees as $employee)
                    <tr>
                        <td>{{$employee->firstname}} {{$employee->lastname}}</td>
                        <td class="text-nowrap">
                            <a class="btn btn-success btn-sm" href="{{route('employee.showDetail',['id'=>$employee->id])}}" data-toggle="tooltip" data-original-title="Edit"> Show </a>
                        </td>
                        @if(Auth::user()->isAllowed('EmployeeController:edit'))
                        <td class="text-nowrap">
                            <a class="btn btn-info btn-sm" href="{{route('employee.edit',['id'=>$employee->id])}}" data-toggle="tooltip" data-original-title="Edit"> Edit </a>
                        </td>
                        @endif
                        
                    </tr>
                    @endforeach @else
                        <tr> No Employee Found</tr>
                    @endif --}}
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
<script type="text/javascript">
$("input.zoho").click(function (event) {
    if ($(this).is(":checked")) {
        $("#div_" + event.target.id).show();
    } 
    else {
        $("#div_" + event.target.id).hide();
    }
});
</script>

<script type="text/javascript">
    $("input.zoho").click(function (event) {
        if ($(this).is(":checked")) {
            $("#div_" + event.target.id).show();
        } else {
            $("#div_" + event.target.id).hide();
        }
    });
</script>
<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            stateSave: true,
        });
    });
</script>
<script src="{{asset('assets/plugins/moment/moment.js')}}"></script>
<script src="{{asset('assets/plugins/footable/js/footable.min.js')}}"></script>
@endpush
@stop
