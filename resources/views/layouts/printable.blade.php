<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
@include('includes.adminHead')
<body class="fix-header fix-sidebar card-no-border">
<div id="main-wrapper">
    <div class="container-fluid pt-3">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-2">
                        <img src="{{asset('assets/images/lasureco-logo.png')}}" alt="logo" width="100%">
                    </div>
                    <div class="col-md-10">
                        <h2>LANAO DEL SUR ELECTRIC COOPERATIVE</h2>
                        <h3>Satellite Ofice, Provincial Capitol Complex, Marawi City</h3>
                        <h4>teamlasureco@ymail.com</h4>
                    </div>
                </div>
                
            </div>
        </div>
        @yield('content')
    </div>
</div>
@include('includes.adminScripts')
@include('sweetalert::alert')
</body>
</html>

