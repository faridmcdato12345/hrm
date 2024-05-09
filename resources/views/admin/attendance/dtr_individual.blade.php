@extends('layouts.admin') @section('title') HRM @endsection
@section('Heading')
    <h3 class="text-themecolor">Attendance Individual </h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
        <li class="breadcrumb-item active">Attendance</li>
        <li class="breadcrumb-item active">Individual</li>
    </ol>
@stop
@section('content')
{{-- @php
use Carbon\Carbon;
@endphp --}}
    <div class="row">
        {{-- @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif --}}
        <div class="col-md-12 col-xlg-12">
            <!-- Row -->
            <div class="row">
                <!-- Column -->
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-row">
                                <div class="round round-md align-self-center round-primary"><i class="far fa-calendar-check"></i></div>
                                <div class="m-l-10 align-self-center">
                                    <h5 class="m-b-0">Present</h5>
                                    <h5 class="m-b-0 font-light">{{$present}} day(s)</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-row">
                                <div class="round round-md align-self-center round-danger"><i class="far fa-calendar-times"></i></div>
                                <div class="m-l-10 align-self-center">
                                    <h5 class="m-b-0">Absent</h5>
                                    <h5 class="m-b-0 font-light">{{$absent}} day(s)</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-row">
                                <div class="round round-md align-self-center round-warning"><i class="far fa-clock"></i></div>
                                <div class="m-l-10 align-self-center">
                                    <h5 class="text-muted m-b-0">Total Rendered</h5>
                                    <h5 class="m-b-0 font-light">{{$rendered}} min(s)</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-row">
                                <div class="round round-md align-self-center round-warning"><i class="far fa-clock"></i></div>
                                <div class="m-l-10 align-self-center">
                                    <h5 class="text-muted m-b-0">Total Late</h5>
                                    <h5 class="m-b-0 font-light">{{$late}} min(s)</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-row">
                                <div class="round round-md align-self-center round-success"><i class="fas fa-calendar-times"></i></div>
                                <div class="m-l-10 align-self-center">
                                    <h5 class="text-muted m-b-0">Leaves</h5>
                                    <h5 class="m-b-0 font-light">{{$leaves}} day(s)</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <form action={{ route('individual.attendance.show') }} method="POST">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="employee">Employee:</label>
                        <select class="form-control" id="employee" name="emp_id">
                            @forelse ($employees as $employee)
                                <option value="{{ $employee->emp_id }}">{{ $employee->full_name }}</option>
                            @empty
                                <option value="" disabled>No Employee Found.</option>
                            @endforelse
                        </select>
                    </div>
                    <span style="float: left;"><input value="{{$datefrom}}" class="form-control" type="date" name="datefrom"></span>
                    <span style="float: left;margin-top: 7px">&nbsp&nbsp To &nbsp&nbsp</span>
                    <span style="float: left;"><input value="{{$dateto}}" class="form-control" type="date" name="dateto"></span>
                    &nbsp&nbsp&nbsp&nbsp<button type="submit" class="btn btn-primary" style="margin-top:2px">Submit</button>
                    </form>                
            </div>
            <div class="table-responsive m-t-40">
                <table id="myTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Am in</th>
                        <th>Am Out</th>
                        <th>Pm In</th>
                        <th>Pm Out</th>
                        <th>Rendered</th>
                        <th>Late</th>
                        <th>Actions</th>
						{{-- @if(Auth::user()->role == 1)
                        <th>Actions</th>
						@endif --}}
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($output as $item)
                    <tr>
                        <td class="text-nowrap">
                            {{$item['date']}}
                        </td>
                        <td class="text-nowrap">
                            {{$item['am_clock_in']}}
                        </td>
                        <td class="text-nowrap">
                            {{$item['am_clock_out']}}
                        </td>
                        <td class="text-nowrap">
                            {{$item['pm_clock_in']}}
                        </td>
                        <td class="text-nowrap">
                            {{$item['pm_clock_out']}}
                        </td>
                        <td class="text-nowrap">
                            {{$item['rendered']}}
                        </td>
                        <td class="text-nowrap">
                            {{$item['late']}}
                        </td>
                        <td class="text-nowrap">
                            <div class="form">
                                <a class="btn btn-info btn-sm" data-toggle="modal" data-target="#edit-{{ $item['num'] }}"> <i class="fas fa-edit text-white"> Edit</i></a>
                                <a class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete-{{ $item['num'] }}"> <i class="fas fa-trash text-white"> Delete</i></a>
                                {{-- Edit modal --}}
                                <div class="modal fade" id="edit-{{ $item['num'] }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('individual.attendance.edit') }}" method="post">
                                                {{ csrf_field() }}
                                                <div class="modal-header">
                                                    <h4>Edit Attendance</h4>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label">Name: {{ $item['name'] }}</label><br>
                                                        <label class="control-label">Date: {{ $item['date'] }}</label>
                                                        <br>
                                                        <label class="control-label">AM In:</label>
                                                        <br>
                                                        <input class="form-control col-6" type="text" value={{ $item['am_clock_in'] }} readonly>
                                                        <input class="form-control col-6" type="time" name="amin" min="07:00" max="11:59">
                                                        <br>
                                                        <label class="control-label">AM Out:</label>
                                                        <br>
                                                        <input class="form-control col-6" type="text" placeholder="{{ $item['am_clock_out'] }}" readonly>
                                                        <input class="form-control col-6" type="time" name="amout" min="07:01" max="12:59">
                                                        <br>
                                                        <label class="control-label">PM In:</label>
                                                        <br>
                                                        <input class="form-control col-6" type="text"  placeholder="{{ $item['pm_clock_in'] }}"readonly>
                                                        <input class="form-control col-6" type="time" name="pmin" min="12:30" max="16:59">
                                                        <br>
                                                        <label class="control-label">PM Out:</label>
                                                        <br>
                                                        <input class="form-control col-6" type="text" placeholder="{{ $item['pm_clock_out'] }}" readonly>
                                                        <input class="form-control col-6" type="time" name="pmout" min="13:01" max="23:59">
                                                        <br>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <input type="hidden" name="id" value="{{ $item['id'] }}">
                                                    <input type="hidden" name="date" value="{{ $item['date'] }}">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                    <button  type="submit" class="btn btn-info btn-ok">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                {{-- Delete modal --}}
                                <div class="modal fade" id="delete-{{ $item['num'] }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('individual.attendance.delete') }}" method="post">
                                                {{ csrf_field() }}
                                                <div class="modal-header">
                                                    <h4>Delete Attendance</h4>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label">Name: {{ $item['name'] }}</label><br>
                                                        <label class="control-label">Date: {{ $item['date'] }}</label>
                                                        <br>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <input type="hidden" name="id" value="{{ $item['id'] }}">
                                                    <input type="hidden" name="date" value="{{ $item['date'] }}">
                                                    <input type="hidden" name="att_am_id" value="{{ $item['att_date_am'] }}">
                                                    <input type="hidden" name="att_pm_id" value="{{ $item['att_date_pm'] }}">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                    <button  type="submit" class="btn btn-danger btn-ok">Remove Attendance?</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </td>
                    </tr>
                    @empty
                        <tr> No Employee Found.</tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@push('scripts')
{{-- <script type="text/javascript">
$("input.zoho").click(function (event) {
    if ($(this).is(":checked")) {
        $("#div_" + event.target.id).show();
    } 
    else {
        $("#div_" + event.target.id).hide();
    }
});
</script> --}}

{{-- <script type="text/javascript">
    $("input.zoho").click(function (event) {
        if ($(this).is(":checked")) {
            $("#div_" + event.target.id).show();
        } else {
            $("#div_" + event.target.id).hide();
        }
    });
</script> --}}
<script>
    $(function () {
        $(document).ready(function() {
            $('#myTable').DataTable({
                stateSave: true,
            });
        });
    });
</script>
<script src="{{asset('assets/plugins/moment/moment.js')}}"></script>

@endpush
@stop