@extends('layouts.admin')
@section('Heading')
    @if(Auth::user()->isAllowed('LeaveController:adminCreate'))
        <button type="button"  onclick="window.location.href='{{route('admin.createLeave')}}'" class="btn btn-info btn-rounded m-t-10 float-right"><span class="fas fa-plus"></span> Add Employee Leave</button>
    @endif
    <h3 class="text-themecolor">Add Attendance</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
        <li class="breadcrumb-item active">Attendance</li>
        <li class="breadcrumb-item active">Add Attendance</li>
    </ol>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline-info">
                <div style="margin-top: 10px;margin-right: 10px">
                    <button type="button" class="btn  btn-info float-right" onclick="window.location.href='{{route('today_timeline')}}'">Back</button>
                    @if(isset($attendance_summary))
                    {{-- <a style="margin-left: 10px" data-toggle="modal" data-target="#confirm-delete{{ $department->id }}" class="btn btn-danger float-left text-white">Delete</a> --}}
                    @endif
                </div>
                @if(isset($attendance_summary))
                <div class="modal fade" id="confirm-delete{{$attendance_summary->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('attendance.delete' ,$attendance_summary->id)}}" method="post">
                                {{ csrf_field() }}
                                <div class="modal-header">
                                    Are you sure you want to delete Attendance Of?
                                </div>
                                <div class="modal-header">
                                    <h4> @foreach($employees as $emp)
                                            @if($emp_id == $emp->id) {{$emp->firstname}} {{$emp->lastname}} @endif
                                        @endforeach</h4>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                    <button  type="submit" class="btn btn-danger btn-ok">Delete</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
                <div class="card-body">
                    <form class="form-horizontal" action="{{route('attendance.storeAttendanceSummaryToday')}}" method='POST'>
                        {{csrf_field()}}
                        <div class="form-body">
                            <h3 class="box-title">Create CheckIn/CheckOut</h3>
                            <hr class="m-t-0 m-b-40">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3">Select Name Here</label>
                                        <div class="col-md-9">
                                            <select class="form-control custom-select" name="employee_id">
                                                <option value="0">Select Employee</option>
                                                @foreach($employees as $emp)
                                                    <option value="{{$emp->id}}" @if($emp_id == $emp->id) selected @endif >{{$emp->firstname}} {{$emp->lastname}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3">Select Date</label>
                                        <div class="col-md-9" >
                                            <input type="date" class="form-control date" name="date" value="{{$current_date}}">
                                            <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3">Time In</label>
                                        <div class="col-md-9">
                                            <input type="datetime-local"  class="form-control" name="time_in" value="">
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group row ">
                                        <label class="control-label text-right col-md-3">Time Out</label>
                                        <div class="col-md-9">
                                            <input type="datetime-local" class="form-control" name="time_out" value="">
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                        </div>
                        <div class="form-actions">
                            <hr>
                            <div class="col-md-12">
                                <div class="row">

                                    <div class="col-md-offset-3 col-md-12">
                                        <button type="submit" class="btn btn-info float-right">Create</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6"> </div>
                        </div>
                    </form>
                    <br>
                    {{--///Dialog Box/// --}}
                <div class="modal fade" id="popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{route('attendance.storeBreak')}}" method='POST'>
                                {{ csrf_field() }}
                                <div class="modal-header" style="margin-right: 20px;">
                                    Add Break
                                </div>
                                <div class="modal-body">
                                    <div class="container-fluid">
                                                <select class="form-control custom-select" name="employee_id" hidden>
                                                    <option value="0">Select Employee</option>
                                                    @foreach($employees as $emp)
                                                        <option value="{{$emp->id}}" @if($emp_id == $emp->id) selected @endif >{{$emp->firstname}} {{$emp->lastname}}</option>
                                                    @endforeach
                                                </select>
                                        <div class="col-md-14">
                                            <label for="date">Date</label><br>
                                            <div class="input-group date1">
                                                <input type="date" class="form-control date" name="date" value="{{$current_date}}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="time_in">Break Start</label>
                                                <div class="input-group timepicker">
                                                    <input type="datetime-local"  class="form-control" name="break_start" value="{{$current_time}}">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="time_out">Break End</label>
                                                <div class="input-group timepicker">
                                                    <input type="datetime-local" class="form-control" name="break_end" value="">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="time_out">Comment</label>
                                                <div class="input-group timepicker">
                                                    <input type="text" class="form-control" name="comment" value="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success create-btn" id="add-btn" >Add Break</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
				</div>
                </div>
        </div>
    </div>
    @push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#delay').hide();

            $('.date').on("change", function(e) {
                        @if($emp_id)
                var url = '{{route('attendance.createBreak')}}/{{$emp_id}}/' + $(this).val();
                        @else
                var url = '{{route('attendance.createBreak')}}/0/' + $(this).val();
                @endif
                if (url) {
                    window.location = url;
                }
                return false;
            });

            $(".custom-select").on('change', function(e){
                var url = '{{route('attendance.createBreak')}}/' + $(this).val() + '/{{$current_date}}';

                if (url) {
                    window.location = url;
                }
                return false;
            });
        });
    </script>
    <script type="text/javascript">
        $(function () {
            $('#datetimepicker2').datetimepicker({
                locale: 'ru'
            });
        });
    </script>
    @endpush
@stop
