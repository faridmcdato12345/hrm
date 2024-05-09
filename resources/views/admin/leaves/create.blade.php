@extends('layouts.admin')
@section('Heading')
	<h3 class="text-themecolor">Add Leave</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		<li class="breadcrumb-item active">Settings</li>
		<li class="breadcrumb-item active">Leave</li>
		<li class="breadcrumb-item active">Apply</li>
	</ol>
@endsection
@section('content')
	<div class="row">
		<div class="col-lg-12">
			<div class="card card-outline-info">
				<div style="margin:10px 10px">
					</h4>
				</div>
				<div class="card-body">
					<div class="alert alert-danger no-leave" style="display:none;">This type of leave has no remaining leave count for this year.</div>
					<form class="form-horizontal" action="{{route('leaves.store')}}" method="post">
						{{csrf_field()}}
						<div class="form-body">
							<h3 class="box-title">Create Leave</h3>
							<hr class="m-t-0 m-b-40">
							<!--/row-->
							<div class="row">
								<div class="col-md-6">
									<div class="form-group row">
										<label class="control-label text-right col-md-3">Leave Type</label>
										<div class="col-md-9">
											<select class="form-control custom-select" name="leave_type">
												<option value="">Choose leave type...</option>
												@foreach($leave_types as $leave_type)
													<option @if(old('leave_type') == $leave_type->id)selected @endif value="{{$leave_type->id}}">{{$leave_type->name}} ({{$leave_type->amount}})</option>
												@endforeach
											</select>
										</div>
									</div>
								</div>
								
								<div class="col-md-6">
									<div class="form-group row">
										<label class="control-label text-right col-md-3">Subject</label>
										<div class="col-md-9">
											<input type="text" class="form-control" placeholder="Enter Subject Here" name="subject" value="{{old('subject')}}">
										</div>
									</div>
								</div>
								<!--/span-->
							</div>
							<!--/row-->
							<div class="row">
								<div class="col-md-6">
									<div class="form-group row">
										<label class="control-label text-right col-md-3">From Date</label>
										<div class="col-md-9">
											<input type="date" class="form-control"   name="datefrom" value="{{old('datefrom')}}"  min="{{Carbon\Carbon::now()->format('Y-m-d')}}">
										</div>
									</div>
								</div>
								
								<div class="col-md-6">
									<div class="form-group row">
										<label class="control-label text-right col-md-3">Description</label>
										<div class="col-md-9">
											<textarea type="text" class="form-control" rows="1" name="description" placeholder="Enter Description Here">{{old('description')}}</textarea>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group row">
										<label class="control-label text-right col-md-3">To Date</label>
										<div class="col-md-9">
											<input type="date" class="form-control" placeholder="dd/mm/yyyy" name="dateto" value="{{old('dateto')}}" min="{{Carbon\Carbon::now()->format('Y-m-d')}}">
										</div>
									</div>
								</div>
							</div>
						</div>
						<hr>
						<div class="form-actions">
							<div class="row">
								<div class="col-md-6">
									<div class="row">
										<div class="col-md-offset-3 col-md-9">
											<button type="submit" class="btn btn-success apply">Apply</button>
											<a href="{{route('leave.index')}}" class="btn btn-inverse">Cancel</a>
										</div>
									</div>
								</div>
								<div class="col-md-6"> </div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('leave_type.create')}}" method="post">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        Create Leave Type
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">Name</label>
                            <input  type="text" name="name" placeholder="Enter Name Here" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Count</label>
                            <input  type="number" name="count" placeholder="Enter leave days count here" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Status</label>
                            <select  name="status"  class="form-control">
                                <option value="1">Active</option>
                                <option value="0">UnActive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button  type="submit" class="btn btn-info btn-ok">Add Leave Type</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
@section('scripts')
<script type="text/javascript">
	$(document).ready(function() {
		$(function () {
			$('#datefrom').datetimepicker({
				format: "YYYY-MM-DD"
			});
			$('#dateto').datetimepicker({
				format: "YYYY-MM-DD"
			});
		});
		$('.custom-select').on('change',function(){
			var leave_id = $(this).find(":selected").val();
			$.ajax({
				data: {
					leave_id:leave_id
				},
				url: "{{route('ajax.employee.leave')}}",
				type: "post",
				dataType: "json",
				success: function(data){
					if(data.leaves == JSON.stringify(data.leave_type)){
						$('.no-leave').css('display','block');
						$('.apply').prop('disabled',true)
					}
					else{
						$('.apply').css('display','inline-block')
						$('.no-leave').css('display','none');
					$('.apply').attr('disabled',false)
					}
					
				},
			});
		});
	});
</script>
@stop
