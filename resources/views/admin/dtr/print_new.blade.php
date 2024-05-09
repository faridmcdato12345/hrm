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
        h5,.daily {
            text-align: center;
        }
        .sig p{
            text-align: center;
        }
        @media print{
			@page {
				size: landscape;
			}
		}
    </style>
</head>
<body onload="window.print()">
    <div class="row row-break">
        @forelse ($attendances as $attendance => $daily)
            <div class="entity col-md-6 emp-div">
                <div>
                    <div>
                        <h5>LASURECO</h5>
                        <p class="daily" style="margin-bottom:0">DAILY TIME RECORD</p>
						<p class="daily">Date: {{$fromPass}} - {{$toPass}}</p>
                        <p class="daily">
							@if($department)
								{{$department->dept_name}}
							@else
								<span>--</span>
							@endif
						</p>
                    </div>
                    <div>
                        <label for="">Name:&nbsp;&nbsp;<span>{{$attendance}}</span></label>
                    </div>
                    <div>
                        <label for="">Job Description:&nbsp;&nbsp;
							<span>
                                @if(empty($daily[0]['designation']))
									<span>--</span>
								@else
									{{$daily[0]['designation']}}
								@endif
                                
							</span>
						</label>
                    </div>
                </div>
                <table class="table-bordered" id="dtr-table">
                    <thead>
                        <th>Date</th>
                        <th>{{ $mode == 'ampm' ? 'AM (In | Out)' : 'Time In' }}</th>
                        <th>{{ $mode == 'ampm' ? 'PM (In | Out)' : 'Time Out' }}</th>
                        <th>Late(Am | Pm)</th>
                        <th>Absent</th>
                    </thead>
                <br>
				
				<tbody>
					@foreach ($daily as $d)
						<tr>
							<td>{{$d['date']}}</td>
							@if ($d['date_string']->isWeekday())
								<td>
									@if($d['clock_in'])
										{{$d['clock_in']}}
									@else
										<span>Absent</span>
									@endif									
								</td>
								<td>
									@if($d['clock_out'])
										{{$d['clock_out']}}
									@else
										<span>--</span>
									@endif
								</td>
								<td class="{{$d['emp_id']}}-work-hour">
									{{$d['late']}}
								</td>
								<td>{{$d['absent']}}</td>
							@else
								<td colspan="2" style="color: red">Weekend</td>
							@endif
						</tr>
					@endforeach
					<tr>
						<td colspan="5" style="font-weight: bold">DEDUCTIONS</td>
					</tr>
					<tr>
						<td>Total Tardiness</td>
						<td class="">
							{{$d['total_tardi']}}
						</td>
						<td colspan="3"></td>
						{{-- <td colspan="3">SALARY</td> --}}
					</tr>
					<tr>
						<td>Total Absences</td>
						<td>{{$d['total_absent']}}</td>
						{{-- <td colspan="3">P {{$d['salary']}}</td> --}}
						<td colspan="3"></td>
					</tr>
                    <tr>
						<td>Total Deduction time</td>
						<td>{{$d['total_time_deduction']}}</td>
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
                        <p>Supervisor Signature</p>
                    </div>
                </div>
				<div class="row sig">
                    <div class="col-md-4">
                        
                    </div>
                    <div class="col-md-4">
                        <hr>
                        <p>Department Manager Signature</p>
                    </div>
					<div class="col-md-4">
                        
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