@extends('layouts.app')
@section('content')
    <div class="login-box card" style="opacity: 0.98">
        <div class="card-body">
            <form class="form-horizontal form-material" id="loginform" method="POST" action="{{ route('login') }}">
                {{ csrf_field() }}
                <h1 style="text-align: center">Human Resource System</h1>
                <br>
                <h3 class = "box-title m-b-20">Sign In</h3>
                <div class="form-group " {{ $errors->has('username') ? ' has-error' : '' }}>
                    <div class="col-xs-12" >
                        <input class="form-control" type="text" name="username" placeholder="Username" id="username" value="{{ old('username') }}" required autofocus>
                    </div>
                    @if ($errors->has('username'))
                        <span class="help-block">
    <strong style="color: red;" >{{ $errors->first('username') }}</strong>
    </span>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}" >
                    <div class="col-xs-12" >
                        <input class="form-control"  name="password"  id="password" type="password"  required="" placeholder="Password"> </div>


                </div>
                <div class="form-group">
                    <div class="d-flex no-block align-items-center">
                        <div class="checkbox checkbox-primary p-t-0">
                            <input id="checkbox-signup" name="remember" type="checkbox"  {{ old( 'remember') ? 'checked' : '' }}  >
                            <label for="checkbox-signup"> Remember me </label>
                        </div>
                        <div class="ml-auto">
                            <a href="{{ route('password.request') }}" id="" class="text-muted"><i class="fa fa-lock m-r-5"></i> Forgot pwd?</a>
                        </div>
                    </div>
                </div>
                <div class="form-group text-center m-t-20">
                    <div class="col-xs-12">
                        <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Log In</button>
                    </div>
                </div>

            </form>
            <form class="form-horizontal" id="recoverform" action="">
                <div class="form-group ">
                    <div class="col-xs-12">
                        <h3>Recover Password</h3>
                        <p class="text-muted">Enter your Email and instructions will be sent to you! </p>
                    </div>
                </div>
                <div class="form-group ">
                    <div class="col-xs-12">
                        <input class="form-control" type="text" required="" placeholder="Email"> </div>
                </div>
                <div class="form-group text-center m-t-20">
                    <div class="col-xs-12">
                        <button class="btn btn-primary btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Reset</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection