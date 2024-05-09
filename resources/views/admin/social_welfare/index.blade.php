@extends('layouts.admin')
@section('Heading')
    <button type="button" class="btn btn-info btn-rounded m-t-10 float-right" data-target="#socialWelfareModal" data-toggle="modal"><span class="fas fa-plus" ></span> Add Social Welfare</button>
    <h3 class="text-themecolor">Social Welfare Management</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		<li class="breadcrumb-item active">Social Welfare</li>
		<li class="breadcrumb-item active">List</li>
	</ol>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Social Welfares</h4>
            <div class="table-responsive m-t-40">
                <table id="myTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            @if(count($socialWelfares) > 0)
                            <th>Name</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($socialWelfares as $socialWelfare)
                    <tr>
                        <td>{{$socialWelfare->name}}</td>
                        <td>
                            @if ($socialWelfare->status == 1)
                                Active
                            @else
                                InActive
                            @endif
                        </td>
                        <td>
                            <a class="btn btn-info btn-sm" href="#" data-toggle="modal" data-target="#edit{{ $socialWelfare->id }}" data-original-title="Edit"> Edit </a>
                            <a class="btn btn-danger btn-sm" href="#" data-toggle="modal" data-target="#confirm-delete{{ $socialWelfare->id }}" data-original-title="Edit"> Delete </a>
                        </td>
                        <div class="modal fade" id="edit{{ $socialWelfare->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{route('social_welfares.update',['id'=>$socialWelfare->id])}}" method="post">
                                        {{ method_field('patch') }}
                                        {{ csrf_field() }}
                                        <div class="modal-header">
                                            Update Social Welfare Service
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label">Name</label>
                                                <input  type="text" name="name" value="{{old('name',$socialWelfare->name)}}" placeholder="Enter name here..." class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Status</label>
                                                <select name="status" class="form-control">
                                                   <option value="1" @if($socialWelfare->status == 1) selected @else @endif>Active</option>
                                                   <option value="0" @if($socialWelfare->status == 0) selected @else @endif>InActive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                            <button  type="submit" class="btn btn-success btn-ok">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="confirm-delete{{ $socialWelfare->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('social_welfares.destroy' , $socialWelfare->id )}}" method="post">
                                        {{ method_field('delete') }}
                                        {{ csrf_field() }}
                                        <div class="modal-header">
                                            Are you sure you want to delete the {{$socialWelfare->name}}?
                                        </div>
                                        <div class="modal-header">
                                            <h4>{{ $socialWelfare->name }}</h4>
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
                        <tr> No Social Welfare Found</tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="socialWelfareModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('social_welfares.store')}}" method="post">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        Create Social Welfare
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">Name</label>
                            <input  type="text" name="name" placeholder="Enter name here..." class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">InActive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button  type="submit" class="btn btn-info btn-ok" id="add_socialWelfare">Save</button>
                    </div>
                </form>
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
