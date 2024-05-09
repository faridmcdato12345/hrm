@extends('layouts.admin') @section('title') HRM @endsection
@section('Heading')
    <button type="button"  onclick="window.location.href='{{route('thirteen.getEmployee')}}'" class="btn btn-danger btn-rounded m-t-10 float-right">Cancel</button>
    <h3 class="text-themecolor">Unclaimed 13 Month</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		<li class="breadcrumb-item">Unclaimed</li>
		<li class="breadcrumb-item active">13 Month</li>
	</ol>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive m-t-40">
                <table id="myTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        @if(count($employees) > 0)
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Department</th>
                        <th>Employment Status</th>
                        <th>Basic Salary</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($employees as $employee)
                    <tr>
                        <input type="hidden" name="" value="{{$employee->id}}" id="employee_id">
                        <td>{{$employee->firstname}} {{$employee->lastname}}</td>
                        <td>{{ ucfirst(trans($employee->designation))}}</td>
                        <td>{{isset($employee->department) ? $employee->department->department_name : ''}}</td>
                        <td>{{$employee->employment_status}}</td>
                        <td>
                            @php
                                $amount = new \NumberFormatter("en_PH",\NumberFormatter::CURRENCY);
                                $subResult = $employee->to_year - $employee->from_year;
                                $salaryAmount = $employee->basic_salary;  
                                $formatted = $amount->format($salaryAmount);  
                            @endphp
                            {{$formatted}}
                        </td>
                        <td class="text-nowrap">
                            <a class="btn btn-info btn-sm add_to_unclaim" data-toggle="modal" data-target="#edit{{ $employee->id }}" data-original-title="Edit"><i class="fas fa-plus text-white "></i></a>
                        </td>
                        <div class="modal fade" id="edit{{ $employee->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{route('thirteen.store')}}" method="post">
                                        {{ csrf_field() }}
                                        <div class="modal-header">
                                            Add {{$employee->firstname}} {{$employee->lastname}}
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="hidden" name="employee_id" value="{{$employee->id}}" id="employee_id">
                                                <label class="control-label">From Year:</label>
                                                <input  type="text" name="from_year" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-labeWWl">To Year:</label>
                                                <input  type="text" name="to_year" class="form-control">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                            <button  type="submit" class="btn btn-success btn-ok">Add</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
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
        var table = $('#myTable').DataTable({
            stateSave: true,
        });

        $.ajax({
            url: "{{route('thirteen.store')}}",
            type: "json",
            method: "post",

        })
    });
</script>
<script src="{{asset('assets/plugins/moment/moment.js')}}"></script>
<script src="{{asset('assets/plugins/footable/js/footable.min.js')}}"></script>
@endpush
@stop