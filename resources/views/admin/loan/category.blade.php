@extends('layouts.admin')
@section('Heading')
    <a href="{{route('loan_type.index')}}" class="btn btn-danger btn-rounded m-t-10 float-right">Back</a>
    <h3 class="text-themecolor">Loan Category</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		<li class="breadcrumb-item active">Loan</li>
		<li class="breadcrumb-item active">Category</li>
	</ol>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Loan Categories</h4>
            <div class="table-responsive m-t-40">
                <table id="myTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            @if(count($loanCategories) > 0)
                            <th>Type</th>
                            <th>Category</th>
                            <th>Contribution Amount</th>
                            <th>Period / Months</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($loanCategories as $loanCategory)
                    <tr>
                        <td>{{$loanCategory->loans->name}}</td>
                        <td>{{$loanCategory->name}}</td>
                        <td>{{$loanCategory->amount}}</td>
                        <td>{{$loanCategory->period}}</td>
                        <td>
                            <a class="btn btn-info btn-sm" href="#" data-toggle="modal" data-target="#edit{{ $loanCategory->id }}" data-original-title="Edit"> Edit </a>
                            <a class="btn btn-danger btn-sm" href="#" data-toggle="modal" data-target="#confirm-delete{{ $loanCategory->id }}" data-original-title="Edit"> Delete </a>
                        </td>
                        <div class="modal fade" id="edit{{ $loanCategory->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{route('loan.category.update',['id'=>$loanCategory->id])}}" method="post">
                                        {{ csrf_field() }}
                                        <div class="modal-header">
                                            Update {{$loanCategory->loans->name}} Category
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label">Name</label>
                                                <input  type="text" name="name" value="{{old('name',$loanCategory->name)}}" placeholder="Enter loan name here..." class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label">Amount</label>
                                                <input  type="number" name="amount" value="{{old('amount',$loanCategory->amount)}}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label">Period/Months</label>
                                                <input  type="number" name="period" value="{{old('amount',$loanCategory->period)}}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label">Status</label>
                                                <select name="status" class="form-control">
                                                <option value="1" @if($loanCategory->status == 1) selected @else @endif>Active</option>
                                                <option value="0" @if($loanCategory->status == 0) selected @else @endif>InActive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                            <button  type="submit" class="btn btn-success btn-ok">Update Loan Type</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="confirm-delete{{ $loanCategory->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('loan.category.delete' , $loanCategory->id )}}" method="post">
                                        {{ csrf_field() }}
                                        <div class="modal-header">
                                            Are you sure you want to delete the {{$loanCategory->name}} Loan Category?
                                        </div>
                                        <div class="modal-header">
                                            <h4>{{ $loanCategory->name }}</h4>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                            <button  type="submit" class="btn btn-danger btn-ok">Delete</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </tr>
                    @endforeach @else
                        <tr> No Loan Found</tr>
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
