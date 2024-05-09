@extends('layouts.admin') @section('title') HRM|{{$title}} @endsection
@section('Heading')
<button type="button"  onclick="window.location.href='{{route('benefit.create')}}'" class="btn btn-info btn-rounded m-t-10 float-right"><span class="fas fa-plus" ></span> Create Benefit</button>
    <h3 class="text-themecolor">Benefits</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		<li class="breadcrumb-item active">Setting</li>
		<li class="breadcrumb-item active">Benefit Management</li>
	</ol>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="float-right">
                {{-- <select class="form-control" id="filter">
                    <option value="select">Select Employees</option>
                    @foreach($filters as $filter)
                    <option value="{{$filter}}" @if($filter==$selectedFilter) selected @endif>{{ucfirst(trans($filter))}}</option>
                    @endforeach
                </select> --}}
            </div>
            {{-- <h4 class="card-title"> {{$active_employees}}  Active / {{$employees->count()}} Employees</h4> --}}
            <div class="table-responsive m-t-40">
                <table id="myTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        @if(count($benefits) > 0)
                        <th>#</th>
                        <th>Name</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($benefits as $benefit)
                    <tr>
                        <td>{{$benefit->id}}</td>
                        <td>{{$benefit->name}}</td>
                        <td>{{$benefit->amount}}</td>
                        <td class="text-nowrap">
                            <a class="btn btn-info btn-sm" data-toggle="modal" data-target="#edit{{ $benefit->id }}" data-original-title="Edit"> <i class="fas fa-pencil-alt text-white "></i></a>
                            <a class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete{{ $benefit->id }}"  data-original-title="Close"> <i class="fas fa-window-close text-white  "></i> </a>
                            <div class="modal fade" id="confirm-delete{{ $benefit->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('benefit.destroys' , $benefit->id )}}" method="post">
                                            {{ csrf_field() }}
                                            <div class="modal-header">
                                                Are you sure you want to delete this Benefit?
                                            </div>
                                            <div class="modal-header">
                                                <h4>{{ $benefit->name }}</h4>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                <button  type="submit" class="btn btn-danger btn-ok">Delete</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <div class="modal fade" id="edit{{ $benefit->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{route('benefit.updates',['id'=>$benefit->id])}}" method="post">
                                        {{ csrf_field() }}
                                        <div class="modal-header">
                                            Update Benefit
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label">Name:</label>
                                                <input  type="text" name="name" value="{{old('name',$benefit->name)}}" placeholder="Enter Benefit name here" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-labeWWl">Amount:</label>
                                                <input  type="text" name="amount" value="{{old('amount',$benefit->amount)}}" placeholder="Enter Benefit amount here" class="form-control">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                            <button  type="submit" class="btn btn-success btn-ok">Update Benefit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </tr>
                    @endforeach @else
                        <tr> No Benefits Found</tr>
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
        $('#myTable').DataTable({
            stateSave: true,
        });
    });
</script>
<script src="{{asset('assets/plugins/moment/moment.js')}}"></script>
<script src="{{asset('assets/plugins/footable/js/footable.min.js')}}"></script>
@endpush
@stop
