+@extends('layouts.admin') @section('title') HRM|{{$title}} @endsection
@section('Heading')
<button type="button"  onclick="window.location.href='{{route('employees')}}'" class="btn btn-danger btn-rounded m-t-10 float-right"> Back</button>
    <h3 class="text-themecolor" style="text-transform: capitalize;">{{$employees->firstname}} {{$employees->lastname}} Details</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		<li class="breadcrumb-item active">People Management</li>
        <li class="breadcrumb-item active">Employees</li>
        <li class="breadcrumb-item active">{{$employees->firstname}} {{$employees->lastname}}</li>
	</ol>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <div id="accordion">
                @if (Auth::user()->isAllowed('EmployeeController:showDetail'))
                <div class="card" style="margin-bottom: 0px;">
                    <div class="card-header" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#details" aria-expanded="true" aria-controls="collapseOne">
                                Details
                            </button>
                        </h5>
                    </div> 
                    <!---Employee Information-->
                    <div id="details" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <h3 class="box-title">Employee Information</h3>
                            <hr class="m-t-0 m-b-40">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3 font-weight-bolder"><strong>First Name:</strong></label>
                                        <div class="col-md-9">
                                            <p style="text-transform: capitalize;">{{$employees->firstname}}</p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3 font-weight-bolder"><strong>Last Name:</strong></label>
                                        <div class="col-md-9">
                                            <p style="text-transform: capitalize;">{{$employees->lastname}}</p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3 font-weight-bolder"><strong>Personal Email:</strong></label>
                                        <div class="col-md-9">
                                            <p>{{$employees->official_email}}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3 font-weight-bolder"><strong>Employment Status:</strong></label>
                                        <div class="col-md-9">
                                            <p style="text-transform: capitalize;">{{$employees->employment_status}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3 font-weight-bolder"><strong>Designation:</strong></label>
                                        <div class="col-md-9">
                                            <p style="text-transform: capitalize;">
                                            @forelse($designations as  $designation)
                                            @if($designation->id == $employees->designation_id)
                                            {{$designation->designation_name}}
                                            @endif
                                            @empty
                                            <option value="">No found designation</option>
                                            @endforelse
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3 font-weight-bolder"><strong>Salary:</strong></label>
                                        <div class="col-md-9">
                                            <p style="text-transform: capitalize;">
                                                @forelse($designations as  $designation)
                                                @if($designation->id == $employees->designation_id)
                                                {{$designation->salary}}
                                                @endif
                                                @empty
                                                <option value="">No Salary</option>
                                                @endforelse
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--/row-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3 font-weight-bolder"><strong>Department:</strong></label>
                                        <div class="col-md-9">
                                            <p style="text-transform: capitalize;">
                                                @if($departments->count() > 0)
                                                    {{$departments[0]->dept_name}}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3 font-weight-bolder"><strong>Gender:</strong></label>
                                        <div class="col-md-9">
                                            <p style="text-transform: capitalize;">{{$employees->gender}}</p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--end employee information-->
                            <!-- Contact information-->
                            <h3 class="box-title">Contact Information</h3>
                            <hr class="m-t-0 m-b-40">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3"><strong>Contact#:</strong></label>
                                        <div class="col-md-9">
                                            <p style="text-transform: capitalize;">{{$employees->contact_no}}</p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3"><strong>City:</strong></label>
                                        <div class="col-md-9">
                                            <p style="text-transform: capitalize;">{{$employees->city}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--/row-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3"><strong>Emergency Contact#:</strong></label>
                                        <div class="col-md-9">
                                            <p style="text-transform: capitalize;">{{$employees->emergency_contact}}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3"><strong>Emergency Contact Person:</strong></label>
                                        <div class="col-md-9">
                                            <p style="text-transform: capitalize;">{{$employees->contact_person}}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3"><strong>Current Address:</strong></label>
                                        <div class="col-md-9">
                                            <p style="text-transform: capitalize;">{{$employees->current_address}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3"><strong>Date Of Birth:</strong></label>
                                        <div class="col-md-9">
                                        <p style="text-transform: capitalize;">{{$employees->date_of_birth}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3"><strong>Permanent Address:</strong></label>
                                        <div class="col-md-9">
                                            <p style="text-transform: capitalize;">{{$employees->permanent_address}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3"><strong>Joining Date:</strong></label>
                                        <div class="col-md-9">
                                            <p style="text-transform: capitalize;">{{$employees->joining_date}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end contact information-->
                            <!--Social welfare-->
                            <h3 class="box-title">Social Welfare Services & Tax</h3>
                            <hr class="m-t-0 m-b-40">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3"><strong>SSS#:</strong></label>
                                        <div class="col-md-9">
                                            <p>{{$employees->sss}}</p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3"><strong>PAG-IBIG#:</strong></label>
                                        <div class="col-md-9">
                                            <p>{{$employees->pag_ibig}}</p>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <!--/row-->
                            <div class="row">
                                <!--/span-->
                                
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3"><strong>PhilHealth#:</strong></label>
                                        <div class="col-md-9">
                                            <p>{{$employees->philhealth}}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-3"><strong>TIN#:</strong></label>
                                        <div class="col-md-9">
                                            <p>{{$employees->tin}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end social welfare-->
                        </div>
                    </div>
                </div>
                @endif
                @if (Auth::user()->isAllowed('EmployeeController:storeEmployeeMemo'))
                <div class="card" style="margin-bottom: 0px;">
                    <div class="card-header" id="headingTwo">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#memo" aria-expanded="false" aria-controls="collapseTwo">
                            Memo
                            </button>
                        </h5>
                    </div>
                    <div id="memo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                    <button type="button" class="btn btn-info" style="margin-left:15px" data-toggle="modal" data-target="#add_memo{{$employees->id}}"> Attach File</button>
                        <div class="card-body">
                            <div class="table">
                                <table id="demo-foo-addrow" class="table table-box m-t-20 table-hover contact-list" data-paging="true" data-paging-size="7">
                                    @if(count($memos) > 0)
                                    <thead>
                                    <tr>
                                        <th>Document Name</th>
                                        <th>Uploaded Date</th>
                                        @if(Auth::user()->hasRole('admin'))
                                        <th>Action</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($memos as $memo)
                                        <tr>
                                            <td>
                                                <a  target="_blank" href="{{asset($memo->url)}}">{{ $memo->file_name }}</a>
                                            </td>
                                            <td>{{$memo->created_at->format('m/d/Y')}}</td>
                                            {{-- <td>
                                                {{ ($file->status == 1) ? 'Active' : 'Inactive' }}
                                            </td> --}}
                                            <td class="row">
                                                <div class="col-sm-2">
                                                    <form action="{{ route('documents.delete' , $memo->id )}}" method="post">
                                                        {{ csrf_field() }}
                                                        <button class="btn btn-danger btn-sm">
                                                            <span class="fas fa-window-close"></span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                    <p class="text-center" style="margin-top:70px;" >No Document Found</p>
                                    @endif
                                    <!--- Modal to add memo file--->
                                    <div class="modal fade" id="add_memo{{ $employees->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{route('employee.store.memo')}}" method="post" enctype="multipart/form-data">
                                                    {{ csrf_field() }}
                                                    <div class="form-body">
                                                        <div class="col-md-12">
                                                            <div class="form-group" style="margin-bottom: 0px;">
                                                                <div class="card-body">
                                                                    <label class="control-label">Document Name</label>
                                                                    <input type="text" name="document_name" class="form-control" placeholder="Enter Document Name">
                                                                    <input type="hidden" name="employee_id" value="{{$employees->id}}">
                                                                </div>
                                                            </div>
                                                            <div class="form-group" style="margin-bottom: 0px;">
                                                                <div class="card-body">
                                                                    <h4 class="card-title">File Upload</h4>
                                                                    <label for="input-file-now">You Can Attatch More Than One File </label>
                                                                    <br>
                                                                    <input type="file" class="form-control" name="document" multiple/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div class="form-actions">
                                                        <div class="card-body">
                                                            &nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-success">Upload</button>
                                                            <button type="button" data-dismiss="modal" class="btn btn-inverse">Cancel</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!---end add modal file--->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="card" style="margin-bottom: 0px;">
                    <div class="card-header" id="headingTwo">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#receivable" aria-expanded="false" aria-controls="collapseTwo">
                            Receivables
                            </button>
                        </h5>
                    </div>
                    <div id="receivable" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                    <button type="button" class="btn btn-info" style="margin-left:15px" data-toggle="modal" data-target="#add_item{{$employees->id}}"> Add Item</button>
                        <div class="card-body">
                            <div class="table">
                                <table id="demo-foo-addrow" class="table table-box m-t-20 table-hover contact-list" data-paging="true" data-paging-size="7">
                                    @if(count($receivables) > 0)
                                    <thead>
                                    <tr>
                                        <th>Property Number</th>
                                        <th>Serial Number</th>
                                        <th>Article Description</th>
                                        <th>Department</th>
                                        <th>Quantity</th>
                                        <th>Date Received</th>
                                        @if(Auth::user()->hasRole('admin'))
                                        <th>Action</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($receivables as $receivable)
                                        <tr>
                                            <td>{{$receivable->property_number}}</td>
                                            <td>{{$receivable->serial_number}}</td>
                                            <td>{{$receivable->name}}</td>
                                            {{-- <td>{{$receivable->department}}</td> --}}
                                            <td>{{$receivable->quantity}}</td>
                                            @if($receivable->created_at)
                                            <td>{{$receivable->created_at->format('m/d/Y')}}</td>
                                            @else
                                            <td>NULL</td>
                                            @endif
                                            <form action="{{route('receivable.clear',['id'=>$receivable->id])}}" method="post">
                                            {{csrf_field()}}
                                            <td><button type="submit" class="btn btn-danger">Clear</button></td>
                                            </form>
                                        </tr>
                                        @endforeach
                                    @else
                                    <p class="text-center" style="margin-top:70px;" >{{$employees->firstname}} {{$employees->lastname}} received nothing.</p>
                                    @endif
                                    <!--- Modal to add receivable--->
                                    <div class="modal fade" id="add_item{{ $employees->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{route('receivables.store')}}" method="post">
                                                    {{ csrf_field() }}
                                                    <div class="form-body">
                                                        <div class="col-md-12">
                                                            <div class="form-group" style="margin-bottom: 0px;">
                                                                <!-- <div class="card-body">
                                                                    <label class="control-label">Property Number</label>
                                                                    <input type="text" name="property_number" class="form-control"> -->
                                                                    <input type="hidden" name="employee_id" value="{{$employees->id}}">
                                                                <!-- </div> -->
                                                                <!-- <div class="card-body">
                                                                    <label class="control-label">Serial Number</label>
                                                                    <input type="text" name="serial_number" class="form-control">
                                                                </div> -->
                                                                <div class="card-body">
                                                                    <label class="control-label">Article Description</label>
                                                                    <input type="text" name="name" class="form-control">
                                                                </div>
                                                                <div class="card-body" style="display:none">
                                                                    <!-- <label class="control-label">Department</label> -->
                                                                    <select name="department" id="department" class="form-control" style="display:none">
                                                                        {{-- @foreach ($departments as $department)
                                                                            <option value="{{$department->id}}">{{$department->dept_name}}</option>
                                                                        @endforeach --}}
                                                                    </select>
                                                                </div>
                                                                <div class="card-body">
                                                                    <label class="control-label">Quantity</label>
                                                                    <input type="text" name="quantity" class="form-control">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div class="form-actions">
                                                        <div class="card-body">
                                                            &nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-success">Save</button>
                                                            <button type="button" data-dismiss="modal" class="btn btn-inverse">Cancel</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!---end receivable modal--->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card" style="margin-bottom: 0px;">
                    <div class="card-header" id="headingTwo">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#datasheet" aria-expanded="false" aria-controls="collapseTwo">
                            201 File
                            </button>
                        </h5>
                    </div>
                    <div id="datasheet" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                    <button type="button" class="btn btn-info" style="margin-left:15px" data-toggle="modal" data-target="#add_item"> Attach File</button>
                        <div class="card-body">
                            <div class="table">
                                <table id="demo-foo-addrow" class="table table-box m-t-20 table-hover contact-list" data-paging="true" data-paging-size="7">
                                    @if(count($pds) > 0)
                                    <thead>
                                    <tr>
                                        <th>File Name</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pds as $p)
                                        <tr>
                                            <td>{{$p->document_name}}</td>
                                            <td>{{$p->created_at}}</td>
                                            <td><a target="_blank" href="{{route('employee.pds.show',['id'=>$p->id])}}" class="btn btn-primary">Show file</button></td>
                                        </tr>
                                        @endforeach
                                    @else
                                    <p class="text-center" style="margin-top:70px;" >{{$employees->firstname}} {{$employees->lastname}} has no 201 file attached</p>
                                    @endif
                                    <!--- Modal to add receivable--->
                                    <div class="modal fade" id="add_item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{route('employee.pds.store')}}" method="post" enctype="multipart/form-data">
                                                    {{ csrf_field() }}
                                                    <div class="form-body">
                                                        <div class="col-md-12">
                                                            <div class="form-group" style="margin-bottom: 0px;">
                                                                <div class="card-body">
                                                                    <label class="control-label">Document Name</label>
                                                                    <input type="text" name="document_name" class="form-control" placeholder="Enter Document Name">
                                                                    <input type="hidden" name="employee_id" value="{{$employees->id}}">
                                                                </div>
                                                            </div>
                                                            <div class="form-group" style="margin-bottom: 0px;">
                                                                <div class="card-body">
                                                                    <h4 class="card-title">File Upload</h4>
                                                                    <label for="input-file-now">You Can Attatch More Than One File </label>
                                                                    <br>
                                                                    <input type="file" class="form-control" name="document" multiple/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div class="form-actions">
                                                        <div class="card-body">
                                                            &nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-success">Upload</button>
                                                            <button type="button" data-dismiss="modal" class="btn btn-inverse">Cancel</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!---end receivable modal--->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $("#filter").change(function(e){
                if ($(this).val()=== "select" ){

                    var url = "{{route('employees')}}/"
                }
                else{
                    var url = "{{route('employees')}}/" + $(this).val();
                }

                if (url) {
                    window.location = url;
                }
                return false;
            });
        });
    </script>
<script type="text/javascript">
$("input.zoho").click(function (event) {
    if ($(this).is(":checked")) {
        $("#div_" + event.target.id).show();
    } 
    else {
        $("#div_" + event.target.id).hide();
    }
});
</script>

<script type="text/javascript">
    $("input.zoho").click(function (event) {
        if ($(this).is(":checked")) {
            $("#div_" + event.target.id).show();
        } else {
            $("#div_" + event.target.id).hide();
        }
    });
</script>
<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            stateSave: true,
        });
    });
</script>
<script src="{{asset('assets/plugins/moment/moment.js')}}"></script>
<script src="{{asset('assets/plugins/footable/js/footable.min.js')}}"></script>
@endpush
@stop
