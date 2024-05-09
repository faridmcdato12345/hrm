@extends('layouts.admin')
@section('Heading')
    <h3 class="text-themecolor">General Payroll</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		<li class="breadcrumb-item active">Payments</li>
		<li class="breadcrumb-item active">Payroll</li>
	</ol>
@stop
@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title">General Payroll</h4>
        <br>
        <form action="{{route('payroll.payment.generate')}}" method="post">
            {{csrf_field()}}
            <div class="form-group">
                <label for="date_from">Date</label>
                <input type="month" name="month" id="month" class="form-control">
            </div>
            <div class="form-group">
                <input type="radio" id="fifteen" name="date" value="15">
                <label for="fifteen">1st-15th</label>
                <input type="radio" id="thirty" name="date" value="30">
                <label for="thirty">16th-30th</label>
            </div>
            <div class="form-group">
                <label for="department">Department/s:</label><br>
                @foreach ($departments as $department)
                <input type="checkbox" id="{{$department->department_name}}" name="department[]" value="{{$department->id}}">
                <label for="{{$department->department_name}}"> {{$department->department_name}}</label><br>
                @endforeach
            </div>
            <button type="submit" class="form-control btn btn-info" style="color:#fff">Generate Payroll</button>
        </form>
    </div>
</div>
@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#myTable').DataTable({
            stateSave: true,
        });
        
    });
</script>
@endpush
@stop
