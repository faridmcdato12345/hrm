@extends('layouts.admin')
@section('Heading')
    <button type="button" onclick="window.location.href='{{route('cash.advance.create')}}'" class="btn btn-info btn-rounded m-t-10 float-right" ><span class="fas fa-plus" ></span> Add Employee</button>
    <h3 class="text-themecolor">Cash Advance</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		<li class="breadcrumb-item active">Cash Advance</li>
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
                            @if(count($uniques) > 0)
                            <th>Employee Name</th>
                            <th>Department</th>
                            <th>Amount</th>
                            <th>Amount deduction per payroll</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($uniques as $data)
                    <tr>
                        <td>{{$data->employees->firstname}} {{$data->employees->lastname}}</td>
                        <td>{{$data->employees->department->department_name}}</td>
                        <td>{{$data->amount}}</td>
                        <td>{{$data->period_amount_deduction}}</td>
                        <td>
                            <a class="btn btn-info btn-sm" href="#" data-toggle="modal" data-target="#edit" data-original-title="Edit"> Edit </a>
                            <a class="btn btn-danger btn-sm" href="#" data-toggle="modal" data-target="#confirm-delete" data-original-title="Edit"> Delete </a>
                        </td>
                    </tr>
                    @endforeach @else
                        <tr> No Cash Advance Found</tr>
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
