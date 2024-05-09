@extends('layouts.admin') @section('title') HRM|{{$title}} @endsection
@section('Heading')
    @if (Auth::user()->isAllowed('EmployeeController:create'))
    <button type="button"  onclick="window.location.href='{{route('employee.create')}}'" class="btn btn-info btn-rounded m-t-10 float-right"><span class="fas fa-plus" ></span> Add Employee</button>
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
                    @foreach($filters as $filter)
                    <option value="{{$filter}}" @if($filter==$selectedFilter) selected @endif>{{ucfirst(trans($filter))}}</option>
                    @endforeach
                </select>
            </div>
            <h4 class="card-title"> {{$active_employees}}  Active / {{$employees->count()}} Employees</h4>
            <div class="table-responsive m-t-40">
                <table id="myTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        @if(count($employees) > 0)
						<th>#</th>
                        <th>Photo</th>
						<th>Name</th>
                        <th>Details</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($employees as $employee)
                    <tr>
						<td>{{$employee->emp_code}}</td>
						<td><img width="100px" src="@if(File::exists(public_path('assets/photos/'.$employee->emp_code.'.jpg'))){{asset('assets/photos/'.$employee->emp_code.'.jpg')}} @else {{asset('assets/images/default.png')}} @endif"></td>
                        <td>{{$employee->firstname}} {{$employee->lastname}}</td>
                        @can('EmployeeController:edit')
                        <td class="text-nowrap">
                            <a class="btn btn-success btn-sm" href="{{route('employee.showDetail',['id'=>$employee->id])}}" data-toggle="tooltip" data-original-title="Edit"> Show Details</a>
                            <a class="btn btn-info btn-sm" href="{{route('employee.edit',['id'=>$employee->id])}}" data-toggle="tooltip" data-original-title="Edit"> Edit Employee</a>
                        </td>
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
    <!---add loan modal-->
    <div class="modal fade" id="add_loan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form  method="post" id="form_id">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        Add Loan
                    </div>
                    <div class="col-md-12">
                        <div class="form-group" id="loan_name">
                            <label class="control-label">Social Welfare</label>
                            <select name="social_welfare_id" id="social_welfare_loan" class="form-control">
                                <option value="" selected>Choose loan name here...</option>
                                @foreach ($socialWelfares as $socialWelfare)
                                    <option value="{{$socialWelfare->id}}">{{$socialWelfare->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="type">Type</label>
                            <select name="loan_id" id="loan" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="amount">Loan Amount</label>
                            <input type="number" name="amount" id="amount" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="monthly_deduction">Monthly Deduction</label>
                            <input type="number" name="monthly_deduction" id="monthly_deduction" class="form-control">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button  type="submit" class="btn btn-success btn-ok">Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!---show loan modal-->
    <div class="modal fade" id="show_loan_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="table-responsive m-t-40">
                    <table id="myTables" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Approved Loan</th>
                                <th>Amount Deduction</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!---show employee membership modal-->
    <div class="modal fade" id="show_employee_membership_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="table-responsive m-t-40">
                    <table id="myTables" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Social Welfare</th>
                                <th>Membership type</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--add membership modal-->
    <div class="modal fade" id="add_membership" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form  method="post" id="form_id">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        Add Employee Membership
                    </div>
                    <div class="col-md-12">
                        <div class="form-group" id="loan_name">
                            <label class="control-label">Social Welfare</label>
                            <select name="social_welfare_id" id="social_welfare" class="form-control">
                                <option value="" selected>Choose loan name here...</option>
                                @foreach ($socialWelfares as $socialWelfare)
                                    <option value="{{$socialWelfare->id}}">{{$socialWelfare->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="type">Type</label>
                            <select name="membership_id" id="membership" class="form-control"></select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button  type="submit" class="btn btn-success btn-ok" id="add_membership">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $("#loan_category").attr('disabled','true');
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
    function openAddLoanModal(employee_id){
        $('#add_loan').modal('show')
        let url = "{{route('add.employee.loan',':id')}}";
        url = url.replace(':id',employee_id);
        $('#add_loan #form_id').attr("action",url);
    }
    function openAddMembershipModal(employee_id){
        let url = "{{route('add.employee.membership',':id')}}";
        url = url.replace(':id',employee_id);
        $('#add_membership #form_id').attr("action",url);
        $("#myTables").dataTable().fnDestroy()
        $('#add_membership').modal('show')
    }
    function openShowMembershipModal(employee_id){
        let url = "{{route('show.employee.membership',':id')}}";
        url = url.replace(':id',employee_id)
        $.ajax({
            url:url,
            type: "get",
            dataType: "json",
            success: function(data){
                $("#show_employee_membership_modal #myTables tbody").html(data)
            }
        });
        $("#show_employee_membership_modal").modal('show')
    }
    $('#social_welfare').on('change',function(){
        var e = $(this).val()
        let url = "{{route('create.employee.membership',':id')}}";
        url = url.replace(':id',e);
        $.ajax({
            url: url,
            type: "get",
            dataType: "json",
            success: function(data){
                if(data.dataTable != ""){
                    $('#membership').html(data.dataTable);
                }
                else{
                    $('#membership').html("<option value='0'>No Available Type</option>");
                }
            }
        });
    })
    $('#social_welfare_loan').on('change',function(){
        var e = $(this).val()
        let url = "{{route('employee.get.loan',':id')}}";
        url = url.replace(':id',e);
        $.ajax({
            url: url,
            type: "get",
            dataType: "json",
            success: function(data){
                if(data.dataTable != ""){
                    $('#loan').html(data.dataTable);
                }
                else{
                    $('#loan').html("<option value='0'>No Available Type</option>");
                }
            }
        });
    })
</script>
@endpush
@stop
