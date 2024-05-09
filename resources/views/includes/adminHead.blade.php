<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('assets/images/favicon.PNG') }}">
    <title>HR|Lasureco</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap Core CSS -->
    <link href="{{asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- chartist CSS -->
    <link href="{{asset('assets/plugins/chartist-js/dist/chartist.min.css') }}" rel="stylesheet">
    <link href="{{asset('assets/plugins/chartist-js/dist/chartist-init.css') }}" rel="stylesheet">
    <link href="{{asset('assets/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css') }}" rel="stylesheet">
    <link href="{{asset('assets/plugins/c3-master/c3.min.css') }}" rel="stylesheet">
    <link href="{{asset('css/style.css') }}" rel="stylesheet">
    <link href="{{asset('css/colors/blue.css') }}" id="theme" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/toastr.min.css')}}">
    <link href="{{asset('assets/plugins/footable/css/footable.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('assets/plugins/wizard/steps.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/plugins/datatables/media/css/dataTables.bootstrap4.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/plugins/html5-editor/bootstrap-wysihtml5.css')}}" />
    <!--dropzone drag and drop frile-->
    <link rel="stylesheet" href="{{asset('css/dropzone.css')}}">
    <link rel="stylesheet" href="{{asset('css/jquery.bootstrap-touchspin.css')}}">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        .ui-datepicker-calendar {
            display: none;
        }
        .ui-datepicker-month {
            display: none;
        }
        .ui-datepicker-next,.ui-datepicker-prev {
            display:none;
        }
        #employeeModal .modal-dialog .modal-content {
            padding:2% !important;
        }
        @media (min-width: 576px){
            #employeeModal .modal-dialog {
                max-width: 735px !important;
                margin: 1.75rem auto;
            }
        }
        @media print{
            @page {
                size: landscape;
            }
            .table th {
                padding: 0rem !important;
            }
        }
    </style>
</head>