@extends('layouts.admin')
@section('Heading')
    <button type="button" class="btn btn-info btn-rounded m-t-10 float-right" data-target="#shareModal" data-toggle="modal"><span class="fas fa-plus" ></span> Add Cooperative Share</button>
    <h3 class="text-themecolor">Cooperative Share Management</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		<li class="breadcrumb-item active">Cooperative Share</li>
		<li class="breadcrumb-item active">List</li>
	</ol>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Cooperative shares</h4>
            <div class="table-responsive m-t-40">
                <table id="myTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            @if(count($shares) > 0)
                            <th>Name</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($shares as $share)
                    <tr>
                        <td>
                            @if($share->bracket != null)
                            {{$share->socialWelfares->name}}({{$share->bracket}} - {{$share->to_bracket}})
                            @else
                            {{$share->socialWelfares->name}}
                            @endif
                        </td>
                        <td>{{$share->amount}}</td>
                        <td>
                            @if ($share->status == 1)
                                Active
                            @else
                                InActive
                            @endif
                        </td>
                        <td>
                            <a class="btn btn-info btn-sm" href="#" data-toggle="modal" data-target="#edit{{ $share->id }}" data-original-title="Edit"> Edit </a>
                            <a class="btn btn-danger btn-sm" href="#" data-toggle="modal" data-target="#confirm-delete{{ $share->id }}" data-original-title="Edit"> Delete </a>
                        </td>
                        <div class="modal fade" id="edit{{ $share->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{route('cooperative_share.update',['id'=>$share->id])}}" method="post">
                                        {{ method_field('patch') }}
                                        {{ csrf_field() }}
                                        <div class="modal-header">
                                            Edit {{$share->socialWelfares->name}} cooperative share Type
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label">Name</label>
                                                <input  type="text" name="name" value="{{old('name',$share->socialWelfares->name)}}" placeholder="Enter share name here..." class="form-control" disabled>
                                            </div>
                                            @if($share->bracket != null)
                                            <div class="form-group">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div>
                                                            <h3>Amount Bracketing</h3>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label">Amount from</label>
                                                            <input type="number" value="{{old('bracket',$share->bracket)}}" name="bracket" placeholder="Enter amount from here..." class="form-control">
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label">Amount to</label>
                                                            <input type="number" name="to_bracket" value="{{old('to_bracket',$share->to_bracket)}}" placeholder="Enter amount from here..." class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="form-group">
                                                <label class="control-label">Amount</label>
                                                <input  type="number" name="amount" value="{{old('name',$share->amount)}}" placeholder="Enter amount here..." class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Status</label>
                                                <select name="status" class="form-control">
                                                   <option value="1" @if($share->status == 1) selected @else @endif>Active</option>
                                                   <option value="0" @if($share->status == 0) selected @else @endif>InActive</option>
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
                        <div class="modal fade" id="confirm-delete{{ $share->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{route('cooperative_share.destroy',['id'=>$share->id])}}" method="post">
                                        {{ method_field('delete') }}
                                        {{ csrf_field() }}
                                        <div class="modal-header">
                                            Are you sure you want to delete the {{$share->socialWelfares->name}} cooperative share?
                                        </div>
                                        <div class="modal-header">
                                            <h4>{{ $share->socialWelfares->name }}</h4>
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
                        <tr> No share Found</tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="shareModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('cooperative_share.store')}}" method="post">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        Create cooperative share
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">Social Welfare</label>
                            <select name="social_walfare_id" id="social_walfare_id" class="form-control">
                                @forelse ($memberships as $membership)
                                    <option value="{{$membership->socialWelfares->id}}">{{$membership->socialWelfares->name}}</option>
                                @empty
                                    <option value="">Not found social welfare</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12" style="padding-bottom: 1%;margin-bottom:10px;">
                        <button type="button" class="btn btn-info btn-formula by_bracket">With Bracket</button>
                        <button type="button" class="btn btn-info btn-formula by_amount" style="display:none;">Amount only</button>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group formula" style="display:none;">
                            <div class="card">
                                <div class="card-body">
                                    <div>
                                        <h3>Amount Bracketing</h3>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Amount from</label>
                                        <input type="number" name="bracket" placeholder="Enter amount from here..." class="form-control input_amount_from">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Amount to</label>
                                        <input type="number" name="to_bracket" placeholder="Enter amount from here..." class="form-control input_amount_to">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group amount">
                            <label for="amount">Amount</label>
                            <input type="number" name="amount" id="amount" class="form-control" placeholder="Enter amount here...">
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
                        <button  type="submit" class="btn btn-info btn-ok" id="add_share">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@push('scripts')
<script>
    $(document).ready(function() {
        $('.btn-formula').click(function(){
            $('.formula,.by_bracket,.by_amount').toggle();
            $('.input_amount_from').val('')
            $('.input_amount_to').val('')
        })
        var table = $('#myTable').DataTable({
            stateSave: true,
        });
                
    });
</script>
@endpush
@stop
