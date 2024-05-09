@extends('layouts.admin') @section('title') HRM| @endsection
@section('Heading')
<button type="button" class="btn btn-info btn-rounded m-t-10 float-right"><span class="fas fa-plus" ></span> Add Formula</button>
    <h3 class="text-themecolor">Formula</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		<li class="breadcrumb-item active">Formula</li>
		<li class="breadcrumb-item active">Overtime</li>
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
                        @if(count($formulas) > 0)
                        <th>Name</th>
                        <th>Formula</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($formulas as $formula)
                    <tr>
                        
                    </tr>
                    @endforeach @else
                        <tr> No Overtime Formula Found</tr>
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
