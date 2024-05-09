@extends('layouts.admin') @section('title') HRM|{{$title}} @endsection
@section('Heading')
    <button type="button"  onclick="window.location.href=''" class="btn btn-info btn-rounded m-t-10 float-right"><span class="fas fa-plus" ></span> Create Benefit</button>
    <h3 class="text-themecolor">Create Benefit</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		<li class="breadcrumb-item active">Setting</li>
        <li class="breadcrumb-item active">Benefit Management</li>
        <li class="breadcrumb-item active">Create Benefit</li>
	</ol>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{route('benefit.store')}}" method="post">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="benefit">Name:</label>
                    <input type="text" name="name" id="name" class="form-control">
                </div>
                <div class="form-group">
                    <label for="amount">Amount:</label>
                    <input type="text" name="amount" id="amount" class="form-control">
                </div>
                <div class="form-group">
                    <button type="submit" name="submit" id="submit" class="btn btn-primary">Save</button>
                    <button type="button" name="cancel" onclick="window.location.href='{{route('benefit.index')}}'" id="cancel" class="btn btn-danger">Cancel</button>
                </div>
            </form>
        </div>
    </div>
@push('scripts')

@endpush
@stop
