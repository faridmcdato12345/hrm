@extends('layouts.admin')
@section('Heading')
    <button type="button"  onclick="window.location.href='{{route('leaves')}}'" class="btn btn-info btn-rounded m-t-10 float-right"><span class="fas fa-plus"></span> Apply For Leave</button>
    <h3 class="text-themecolor">My Leaves</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
        <li class="breadcrumb-item active">Attendance</li>
        <li class="breadcrumb-item active">My Leaves</li>
    </ol>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle"></h6>
                    <div class="table">
                        <table id="demo-foo-addrow" class="table m-t-30 table-hover contact-list" data-paging="true" data-paging-size="7">
                            <thead>
                            @if(count($leaves) > 0)
                                <tr>
                                    <th><strong>Leave Type</strong></th>
                                    <th><strong>Date From</strong></th>
                                    <th><strong>Date To</strong></th>
                                    <th><strong>Subject</strong></th>
                                    <th><strong>Status</strong></th>
                                    @if(Auth::user()->id == 1)
                                        <th><strong>Actions</strong></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($leaves as $leave)
                                <tr>
                                    <td>{{$leave->leaveType->name}}</td>
                                    <td>{{Carbon\Carbon::parse($leave->datefrom)->format('Y-m-d')}}</td>
                                    <td>{{Carbon\Carbon::parse($leave->dateto)->format('Y-m-d')}}</td>
                                    <td>{{$leave->subject}}</td>
                                    <td>
                                        @if($leave->dm_flag == 1 && $leave->ogm_flag == 0)
                                        {{($leave->status != '') ? $leave->status .' by the Supervisor but pending at OGM' : 'Pending'}}
                                        @endif
                                        @if($leave->dm_flag == 1 && $leave->ogm_flag == 1)
                                        {{($leave->status != '') ? $leave->status .' by the Supervisor and at OGM' : 'Pending'}}
                                        @endif
                                        @if($leave->dm_flag == 0 && $leave->ogm_flag == 0)
                                        {{($leave->status != '') ? $leave->status  : 'Pending'}}
                                        @endif
                                    </td>
                                    <td class=" row">
                                        @if(
                                            (strtolower($leave->status) == 'pending' || $leave->status == '')
                                        ) 
                                        <form action="{{route('leave.delete',['id'=>$leave->id])}}" method="post">
                                            {{ csrf_field() }}
                                            <button class=" btn btn-danger btn-sm " type="submit"><i class="fas fa-window-close text-white "></i></button>
                                        </form>
                                        &nbsp;
                                        <a class="btn btn-info btn-sm" href="{{route('leave.edit',['id'=>$leave->id])}}" data-toggle="tooltip" data-original-title="Edit"> <i class="fas fa-pencil-alt text-white "></i></a>
                                        &nbsp;
                                        @endif
                                        <a class="btn btn-info btn-sm" href="{{route('leave.show',['id'=>$leave->id])}}" data-toggle="tooltip" data-original-title="Show"> <i class="fas fa-eye text-white "></i></a>
                                    </td>
                                </tr>
                            @endforeach @else
                                <p style="text-align: center;">No leave found.</p>

                            @endif

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script type="text/javascript">
            $(".update_status").on('change', function (event) {
                if ($(this).val() !== '') {
                    location.href = "{{url('/')}}/leave/updateStatus/" + $(this).attr('id') + '/' + $(this).val();
                }
            });
        </script>
    @endpush
@stop