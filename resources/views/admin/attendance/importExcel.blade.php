{{-- @extends('layouts.admin')
@section('Heading')
    <h3 class="text-themecolor">Import Excel</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
        <li class="breadcrumb-item active">Import Excel</li>
    </ol>
@stop
@section('content')
<div class="panel panel-default">
    <form action="{{route('save.excel')}}" class="dropzone">
        <div class="fallback">
            <input name="file" type="file" multiple />
        </div>
    </form>
</div>
@stop --}}
@extends('layouts.admin')
@section('Heading')
	<h3 class="text-themecolor">Upload Document</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
		<li class="breadcrumb-item active">Settings</li>
		<li class="breadcrumb-item active">Import Excel File</li>
		<li class="breadcrumb-item active">Upload</li>
	</ol>
@stop
@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card card-outline-info">
			<div class="card-body">
				<form action="{{asset('documents/upload')}}" method="post" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="form-body">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Document Name</label>
								<input type="text" name="document_name" class="form-control" placeholder="Enter Document Name">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-6 col-md-6">
								<div class="card-body">
									<h4 class="card-title">File Upload</h4>
									<label for="input-file-now">You Can Attach More Than One File </label>
									<br>
									<input type="file" class="form-control" name="document" multiple/>
								</div>
						</div>
					</div>
				<br>
					<div class="form-actions">
						&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-success">Upload</button>
						<button type="button" onclick="window.location.href='{{route('documents')}}'" class="btn btn-inverse">Cancel</button>
					</div>
				</form>
			</div>
		</div>
    </div>
</div>
@stop