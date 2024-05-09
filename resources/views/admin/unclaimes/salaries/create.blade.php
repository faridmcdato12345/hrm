@extends('layouts.admin')
@section('Heading')
    <h3 class="text-themecolor">Add Employee</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
        <li class="breadcrumb-item active">Unclaimed Salary</li>
        <li class="breadcrumb-item active">Add employee</li>
    </ol>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{route('salary.store')}}" method="POST">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="employee">Employee:</label>
                    <select name="employee_id" id="employee_id" class="form-control">
                        @foreach ($employees as $employee)
                            <option id="{{$employee->id}}" value="{{$employee->id}}">{{$employee->firstname}} {{$employee->lastname}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="employee">From Date:</label>
                    <input type="date" name="from_date" id="from_date" class="form-control">
                </div>
                <div class="form-group">
                    <label for="employee">To Date:</label>
                    <input type="date" name="to_date" id="to_date" class="form-control">
                </div>
                <hr>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            <button type="submit" class="btn btn-success">Add Employee</button>
                        <button type="button" onclick="window.location.href='{{route('salary.getEmployee',['id'=>''])}}'" class="btn btn-inverse">Cancel</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection