@extends('layouts.admin')
@section('Heading')
    <button type="button" class="btn btn-info btn-rounded m-t-10 float-right" data-toggle="modal" data-target="#create"><span class="fas fa-plus"></span> Create Time-in</button>
    <h3 class="text-themecolor">Time-in</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
        <li class="breadcrumb-item active">Settings</li>
        <li class="breadcrumb-item active">Time-in</li>
    </ol>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle"></h6>
                    <div class="table-responsive m-t-40">
                        <table id="myTable" class="display nowrap table table-hover table-striped table-bordered" data-paging="true" data-paging-size="7">
                            <thead>
                                <tr>
                                    <th>Time-in</th>
									<th>Margin</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($timeIns as $timeIn)
                                <tr>
                                    <td>{{Carbon\Carbon::parse($timeIn->time)->format('g:i A')}}</td>
									<td>{{Carbon\Carbon::parse($timeIn->margin)->format('g:i A')}}</td>
                                    <td>@if($timeIn->status==1)
                                        Active
                                        @else
                                            InActive
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        <a class="btn btn-info btn-sm" data-toggle="modal" data-target="#edit{{ $timeIn->id }}"   data-original-title="Edit"> <i class="fas fa-pencil-alt text-white"></i></a>
        
                                    </td>
                                    <div class="modal fade" id="edit{{ $timeIn->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{route('timein.update',['id'=>$timeIn->id])}}" method="post">
													{{ method_field('PATCH') }}
                                                    {{ csrf_field() }}
                                                    <div class="modal-header">
                                                        Update Time-in
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Time</label>
                                                            <input  type="time" name="time" value="{{old('time',$timeIn->time)}}" class="form-control">
                                                        </div>
                                                       <div class="form-group">
                                                            <label class="control-label">Margin</label>
                                                            <input  type="time" name="margin" value="{{old('margin',$timeIn->margin)}}" class="form-control">
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label">Status</label>
                                                            <select  name="status"  class="form-control">
                                                                <option value="1" @if($timeIn->status==1)Selected @endif>Active</option>
                                                                <option value="0" @if($timeIn->status==0)Selected @endif>InActive</option>
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
                                </tr>
                            @endforeach 
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('timein.store')}}" method="post">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        Create Time-in
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">Time</label>
							<input type="time" id="time" name="time" class="form-control">
                        </div>
						<div class="form-group">
                            <label class="control-label">Margin</label>
							<input type="time" id="margin" name="margin" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Status</label>
                            <select  name="status"  class="form-control">
                                <option value="1">Active</option>
                                <option value="0">InActive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button  type="submit" class="btn btn-info btn-ok">Save</button>
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
