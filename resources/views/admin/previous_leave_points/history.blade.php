@extends('layouts.admin') @section('title') HRM @endsection
@section('Heading')
<button type="button" onclick="window.location.href='{{route('report.leave')}}'" class="btn btn-danger btn-rounded m-t-10 float-right"> Back</button>
    <h3 class="text-themecolor" style="text-transform:capitalize;">{{$employee->firstname}} {{$employee->lastname}}</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		<li class="breadcrumb-item active">History Leave Earned</li>
	</ol>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            {{-- <h4 class="card-title"> Active / {{$employees->count()}} Employees</h4> --}}
            <div class="table-responsive m-t-40">
                <table id="myTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        @if(count($leaves) > 0)
                        <th>Vacation Leave</th>
                        <th>Sick Leave</th>
                        <th>Grand Total</th>
                        <th>Applied on</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($leaves as $leave)
                    <tr>
                        <td>{{($leave->vl)/100}}</td>
                        <td>{{($leave->sl)/100}}</td>
                        <td>{{($leave->previous_total_earned)/100}}</td>
                        <td>{{$leave->created_at}}</td>
                    </tr>
                    @endforeach @else
                        <tr> No Leave Earned</tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@push('scripts')
<script>
    $(document).ready(function() {
        let table = $('#myTable').DataTable({
            stateSave: true,
        });
    });
</script>
<script src="{{asset('assets/plugins/moment/moment.js')}}"></script>
<script src="{{asset('assets/plugins/footable/js/footable.min.js')}}"></script>
@endpush
@stop
