@extends('layouts.admin')
@section('Heading')
    <button type="button" onclick="window.location.href='{{route('cash.advance.index')}}'" class="btn btn-danger btn-rounded m-t-10 float-right" >Back</button>
    <h3 class="text-themecolor">Cash Advance</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
        <li class="breadcrumb-item">Cash Advance</li>
        <li class="breadcrumb-item active">Employee</li>
	</ol>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Cash Advance</h4>
            <div class="table-responsive m-t-40">
                <table id="myTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            @if(count($employees) > 0)
                            <th>Employee Name</th>
                            <th>Job</th>
                            <th>Department</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($employees as $employee)
                    <tr>
                        <td>{{$employee->firstname}} {{$employee->lastname}}</td>
                        <td>{{$employee->designation}}</td>
                        <td>{{$employee->department->department_name}}</td>
                        <td>
                            <a class="btn btn-info btn-sm" href="#" data-toggle="modal" data-target="#add{{$employee->id}}" data-original-title="Edit">Add</a>
                        </td>
                        <div class="modal fade" id="add{{ $employee->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{route('cash.advance.store',['id'=>$employee->id])}}" method="post">
                                        {{ csrf_field() }}
                                        <div class="modal-header">
                                            Add Cash Advance to {{$employee->firstname}} {{$employee->lastname}}
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label">Amount</label>
                                                <input  type="number" name="amount" value="" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Monthly Deduction</label>
                                                <input  type="number" name="period" value="" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Deduction starting date</label>
                                                <input  type="date" name="deduction_starting_date" value="" class="form-control">
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
                        <tr>
                            <td>No Cash Advance Found</td>
                        </tr>
                    @endif
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
