@extends('layouts.admin')
@section('Heading')
    <h3 class="text-themecolor">Attendance</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
        <li class="breadcrumb-item active">Attendance</li>
    </ol>
@stop
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading text-center">
            <div><b style="text-align: center;">Create Attendance</b></div>
        </div>
        <div class="row">
            <div class="panel-body">
                <form action="{{route('post.manual.attendance')}}" method='POST'>
                    {{csrf_field()}}
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="date">Select Date</label></br>
                                <input type="date" name="date" id="date" class="datepickstyle">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <button class="btn btn-success create-btn" id="add-btn"  type="submit" > Create</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <script type="text/javascript">
        </script>
    </div>
@stop