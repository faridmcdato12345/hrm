@extends('layouts.admin') @section('title') HRM| @endsection
@section('Heading')
<button type="button"  onclick="window.location.href='#'" class="btn btn-info btn-rounded m-t-10 float-right"><span class="fas fa-plus" ></span> Add Employee</button>
    <h3 class="text-themecolor">Unclaimed Benefits</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		<li class="breadcrumb-item active">Overtime</li>
		<li class="breadcrumb-item active">List</li>
	</ol>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            {{-- <h4 class="card-title"> {{$active_employees}}  Active / {{$employees->count()}} Employees</h4> --}}
            <div class="table-responsive m-t-40">
                <table id="myTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        @if(count($overtimes) > 0)
                        <th>Employee</th>
                        <th>Type</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($overtimes as $overtime)
                    <tr>
                    </tr>
                    @endforeach @else
                        <tr> No Overtime Found</tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@push('scripts')
<script src="{{asset('assets/plugins/moment/moment.js')}}"></script>
<script src="{{asset('assets/plugins/footable/js/footable.min.js')}}"></script>
@endpush
@stop
