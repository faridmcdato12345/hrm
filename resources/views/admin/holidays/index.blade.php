@extends('layouts.admin')
@section('Heading')
    
    <h3 class="text-themecolor">Holidays</h3>
    @if(session()->has('message'))
    <div class="alert alert-success" style="padding: 10px; background-color: #f0f0f0; border: 1px solid #ccc; color: green; display: none;" id="flash-message">
        {{ session('message') }}
    </div>
    @endif
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
        <li class="breadcrumb-item active">Settings</li>
        <li class="breadcrumb-item active">Holidays
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle"></h6>
                    <button type="button" class="btn btn-info btn-rounded m-t-10 float-right" data-toggle="modal" data-target="#create">Fetch National Holidays</button>
                    <button type="button" class="btn btn-info btn-rounded m-t-10 float-right" data-toggle="modal" data-target="#create2">Add Local Holidays</button>
                    <span style="float: left;"><input id="selectDate" value="{{$year}}" class="form-control" type="number" min="1900" max="9999" name="year"></span>
                   
                    <div class="table-responsive m-t-40">
                        
                        <table id="myTable" class="display nowrap table table-hover table-striped table-bordered" data-paging="true" data-paging-size="7">
                            <thead>
                            @if($holidays->count() > 0)
                                <tr>
                                <td>#</td>
                                <th> Name</th>
                                <th> Dates</th>
                                <th> Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($holidays as $key => $holiday)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$holiday->holiday_name}}</td>
                                        <td>{{\Carbon\Carbon::parse($holiday->date_from)->format('M d, Y').' to '.\Carbon\Carbon::parse($holiday->date_to)->format('M d, Y')}}</td>
                                        <td class="text-nowrap">
                                            {{-- <a class="btn btn-info btn-sm" data-toggle="modal" data-target="#update"> <i class="fas fa-pencil-alt text-white"></i></a> --}}
                                            <a class="btn btn-info btn-sm" data-toggle="modal" data-target="#updateModal-{{ $holiday->id }}"> <i class="fas fa-pencil-alt text-white"></i></a>
                                            <a class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal-{{ $holiday->id }}"> <i class="fas fa-trash text-white  "></i> </a>
                                            {{-- <a class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete"> <i class="fas fa-trash text-white  "></i> </a> --}}
                                            {{-- Update modal --}}
                                            <div class="modal fade" id="updateModal-{{ $holiday->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('holidays.update')}}" method="post">
                                                            {{ csrf_field() }}
                                                            <div class="modal-header">
                                                                Move Holiday Dates
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label class="control-label">Name:</label>
                                                                    <br>
                                                                    <input class="form-control" type="text" name="name" placeholder="{{ $holiday->holiday_name }}" required>
                                                                    <br>
                                                                    <label class="control-label">Date From:</label>
                                                                    <br>
                                                                    <input class="form-control" type="date" name="date_from" required>
                                                                    <br>
                                                                    <br>
                                                                    <label class="control-label">Date To:</label>
                                                                    <br>
                                                                    <input class="form-control" type="date" name="date_to" required>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="{{ $holiday->id }}">
                                                                <input type="hidden" name="year" value="{{ $holiday->year }}">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                                <button  type="submit" class="btn btn-info btn-ok">Update</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Delete modal --}}
                                            <div class="modal fade" id="deleteModal-{{ $holiday->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('holidays.delete')}}" method="post">
                                                            {{ csrf_field() }}
                                                            <div class="modal-header">
                                                                {{ $holiday->holiday_name }}
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label class="control-label" style="margin-top: 10px">Are you sure you want to delete?</label>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="{{ $holiday->id }}">
                                                                <input type="hidden" name="year" value="{{ $holiday->year }}">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                                <button  type="submit" class="btn btn-info btn-ok">Delete</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach @else
                                <tr> No Holidays Found</tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('holidays.fetch') }}" method="post">
                    {{ csrf_field() }}
                    <div class="modal-header">
                       Get All Philippine National Holidays This Year
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label" style="margin-top: 5px">Year</label>
                            <input  type="number" min="1900" max="9999"  name="year" placeholder="Enter year here" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button  type="submit" class="btn btn-info btn-ok">Proceed</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="create2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('holidays.store') }}" method="post">
                    {{ csrf_field() }}
                    <div class="modal-header">
                       Add Local Holiday
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label" style="margin-top: 5px">Year</label>
                            <input  type="number"  min="1900" max="9999"  name="year" placeholder="Enter year here" class="form-control" required>
                            <label class="control-label" style="margin-top: 5px">Name</label>
                            <input  type="text" name="holiday_name" placeholder="Enter Holiday Name Here" class="form-control" required>
                            <label class="control-label" style="margin-top: 5px">Date From</label>
                            <input  type="date" name="date_from" placeholder="Enter Holiday Date From Here" class="form-control" required>
                            <label class="control-label" style="margin-top: 5px">Date To</label>
                            <input  type="date" name="date_to" placeholder="Enter Holiday Date To Here" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button  type="submit" class="btn btn-info btn-ok">Proceed</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#myTable').DataTable({
        stateSave: true,
    });        
});
$(document).ready(function () {
    $("#selectDate").change(function(e){
        var url = "{{route('holidays')}}/" + $(this).val();

        if (url) {
            window.location = url;
        }
        return false;
    });
    });
    // Get the flash message element
    const flashMessage = document.getElementById('flash-message');

    // Show the flash message and set a timer to hide it after 10 seconds
    if (flashMessage) {
        flashMessage.style.display = 'block';
        setTimeout(function() {
            flashMessage.style.display = 'none';
        }, 5000); // 10000 milliseconds (10 seconds)
    }
</script>
@endpush
@stop