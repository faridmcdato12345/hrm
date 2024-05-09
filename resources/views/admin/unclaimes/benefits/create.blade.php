@extends('layouts.admin')
@section('Heading')
    <h3 class="text-themecolor">Add Employee</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
        <li class="breadcrumb-item active">Unclaimed benefit</li>
        <li class="breadcrumb-item active">Add Employee</li>
    </ol>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{route('unclaim.benefit.store')}}" method="POST">
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
                    <label for="employee">Benefit:</label>
                    <select name="benefit_id" id="benefit_id" class="form-control">
                        @foreach ($benefits as $benefit)
                            <option id="{{$benefit->id}}" value="{{$benefit->id}}">{{$benefit->name}}</option>
                        @endforeach
                    </select>
                </div>
                <hr>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            <button type="submit" class="btn btn-success">Save</button>
                            <button type="button" onclick="window.location.href='{{route('unclaim_benefit.getEmployee',['id'=>''])}}'" class="btn btn-inverse">Cancel</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection