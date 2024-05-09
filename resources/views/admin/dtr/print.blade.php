<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="{{asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        body{
            width: 100%;
            font-size: 12px;
        }
        .entity{
            display: inline-block;
            height: 750px;
            min-height: 100px;
        }
        label{
            font-weight: bold;
        }
        table{
            width: 100%;
        }
        table td, table th{
            text-align: center;
        }
        h5, .daily {
            text-align: center;
        }
        .sig p{
            text-align: center;
        }
        @media print{@page {size: landscape}}
    </style>
</head>
<body onload="window.print()">
    <div class="row">
        @forelse ($attendances as $attendance)
            <div class="entity col-md-4 emp-div">
                <div>
                    <div>
                        <h5>LASURECO</h5>
                        <p class="daily" style="margin-bottom:0">DAILY TIME RECORD</p>
						<p class="daily">Date: {{$from_date}} - {{$to_date}}</p>
                        <p class="daily">
							@if(empty($attendance[0]['emp_dept_name']->dept_name))
								<span>--</span>
							@else
								{{$attendance[0]['emp_dept_name']->dept_name}}
							@endif
						</p>
                    </div>
                    <div>
                        <label for="">Name:&nbsp;&nbsp;<span>{{$attendance[0]['emp_name']}}</span></label>
                    </div>
                    <div>
                        <label for="">Job Description:&nbsp;&nbsp;
							<span>
								@if(empty($attendance->designation))
									<span>--</span>
								@else
									{{$attendance[0]['designation']}}
								@endif
							</span>
						</label>
                    </div>
                </div>
                <table class="table-bordered" id="dtr-table">
                    <thead>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Late</th>
                        <th>Absent</th>
                    </thead>
                    <tbody>
                        @foreach ($diffs as $d)
                            <tr>
                                <td>{{$d->toDateString()}}</td>
                                @if ($d->isWeekday())
                                    <td>
                                        @foreach ($attendance as $att)
                                        @if($d->toDateString() == $att['date'])
											@if($att['clock_in'])
												{{Carbon\Carbon::parse($att['clock_in'])->format('g:i A')}}
											@else
												<span>Absent</span>
											@endif
                                        
                                        @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($attendance as $att)
                                        @if($d->toDateString() == $att['date'])
											@if($att['clock_out'])
												{{Carbon\Carbon::parse($att['clock_out'])->format('g:i A')}}
											@else
												<span>--</span>
											@endif
                                        
                                        @endif
                                        @endforeach
                                    </td>
									 <td class="{{$attendance[0]['emp_id']}}-work-hour">
									@foreach ($attendance as $att)
										@if($d->toDateString() == $att['date'])
										{{$att['late']}}									
										@endif
									 @endforeach
                                    </td>
                                    <td></td>
                                @else
                                    <td colspan="4" style="color: red">Weekend</td>
                                @endif
                                
                                {{-- @foreach($attendance as $att)
                                    @if($d->toDateString() == $att->date)
                                    <td>{{$att->first_timestamp_in->format('g:i A')}}</td>
                                    <td>{{$att->last_timestamp_out->format('g:i A')}}</td>
                                    <td></td>
                                    <td></td>
                                    @else
                                    <td colspan="3" style="color:red;">Absent</td>
                                    <td style="color:red;">8</td>
                                    @endif
                                @endforeach --}}
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="5" style="font-weight: bold">DEDUCTIONS</td>
                        </tr>
                        <tr>
                            <td>Total Tardiness</td>
                            <td class="">
                            </td>
                            <td>Amount</td>
                            <td colspan="2">
                                
                            </td>
                        </tr>
                        <tr>
                            <td>Total Absences</td>
                            <td></td>
                            <td>Amount</td>
                            <td colspan="2"></td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <div class="row sig">
                    <div class="col-md-6">
                        <hr>
                        <p>Employee Signature</p>
                    </div>
                    <div class="col-md-6">
                        <hr>
                        <p>Dept Mngr/Sup Sig.</p>
                    </div>
                </div>
            </div>
        @empty
            <div>No Record Found</div>
        @endforelse
        
    </div>
<script src="{{asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
</body>
</html>