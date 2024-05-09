@extends('layouts.admin') @section('title') HRM| @endsection
@section('Heading')
    <h3 class="text-themecolor">DTR</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
        <li class="breadcrumb-item active">DTR</li>
    </ol>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{route('dtr.print')}}" method="post">
                {{csrf_field()}}
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="month">From:</label>
                            <div>
                                <input type="date" name="from_date" id="from_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="month">To:</label>
                            <div>
                                <input type="date" name="to_date" id="to_date" class="form-control">
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="month">Department:</label>
                            <div>
                                <select name="dept_id" id="dept_id" class="form-control">
                                    @forelse ($departments as $department)
                                        <option value="{{$department->id}}">{{$department->dept_name}}</option>
                                    @empty
                                        <option value="0">No Department</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="month">Mode:</label>
                            <div>
                                <select name="mode" class="form-control">
                                        <option value="wholeday">Whole Day</option>
                                        <option value="ampm">AM/PM</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div>
                               <button type="submit" class="btn btn-primary form-control text-light">PRINT</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
