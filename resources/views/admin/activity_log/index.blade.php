@extends('layouts.admin') @section('title') HRM| @endsection
@section('Heading')
    <h3 class="text-themecolor">User Activity Log</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
        <li class="breadcrumb-item active">User activity log</li>
    </ol>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table" id="datatable">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Subject</th>
                        <th>Action by</th>
                        <th>Action Date</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@stop
