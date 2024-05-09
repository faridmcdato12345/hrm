@extends('layouts.admin') @section('title') HRM| @endsection
@section('Heading')
<button type="button"  onclick="window.location.href='{{route('unclaim.benefit.create')}}'" class="btn btn-info btn-rounded m-t-10 float-right"><span class="fas fa-plus" ></span> Add Employee</button>
    <h3 class="text-themecolor">Unclaimed Benefits</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		<li class="breadcrumb-item active">Unclaimed</li>
		<li class="breadcrumb-item active">Benefits</li>
	</ol>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="card-title"> {{$active_employees}}  Active / {{$employees->count()}} Employees</h4>
            <div class="table-responsive m-t-40">
                <table id="myTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        @if(count($employees) > 0)
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Department</th>
						<th>Employment Status</th>
						<th>Benefit</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($employees as $employee)
                    <tr>
                        <input type="hidden" name="" value="{{$employee->id}}" id="id">
                        <td>{{$employee->employees->firstname}} {{$employee->employees->lastname}}</td>
                        <td>{{ ucfirst(trans($employee->employees->designation))}}</td>
                        <td>{{isset($employee->employees->department) ? $employee->employees->department->department_name : ''}}</td>
                        <td>{{$employee->employees->employment_status}}</td>
                        <td>{{$employee->benefits->name}}</td>
                        <td class="text-nowrap">
                            <a class="btn btn-info btn-sm claim_button" href="#" data-toggle="tooltip" data-original-title="Edit">Claim</a>
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
        let table = $('#myTable').DataTable({
            stateSave: true,
        });
        $('.claim_button').click(function(){
            let id = $('#id').val()
            let urlUpdate = "{{route('unclaim.benefit.update',':id')}}";
            urlUpdate = urlUpdate.replace(':id',id);
            $.ajax({
                url: urlUpdate,
                type: "json",
                data: {flag: 1},
                method: "PATCH",
                success:function(data){
                    location.reload()
                }
            })
        })
    });
</script>
<script src="{{asset('assets/plugins/moment/moment.js')}}"></script>
<script src="{{asset('assets/plugins/footable/js/footable.min.js')}}"></script>
@endpush
@stop
