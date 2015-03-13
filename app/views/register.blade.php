@extends('layouts.main')

@section('content')
    <div class="pure-u-1 pure-u-md-1-3">
    </div>
    <div class="pure-u-1 pure-u-md-1-3">
        <div class="center-wrapper">
            <div class="login-box container">
                {{ Form::open(array('url' => 'user/register', 'class'=>'pure-form')) }}
                   <h1>Register</h1>
                    <!-- if there are login errors, show them here -->
                    @if(Session::has('errors'))
                        <div class="pure-alert pure-alert-error">
                            <span class="sr-only">Error:</span>
                            <i class="fa fa-exclamation-circle"></i>
                            <span>{{ $errors->first('name') }}</span>
                            <span>{{ $errors->first('email') }}</span>
                            <span>{{ $errors->first('username') }}</span>
                            <span>{{ $errors->first('password') }}</span>
                            <span>{{ $errors->first('password_confirmation')}}</span>
                            <span>{{ $errors->first('captcha')}}</span>
                        </div>
                    @endif

                    <label for="name" class="sr-only">Name</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                        {{ Form::text('name', Input::old('name'),
                        array('id'=>'name','class'=>'form-control','placeholder' => 'Name (Not Required)')) }}
                    </div>
                    <label for="username" class="sr-only">Username</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                        {{ Form::text('username', Input::old('username'),
                        array('id'=>'username','class'=>'form-control','placeholder' => 'Username')) }}
                    </div>
                    <label for="email" class="sr-only">E-mail</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                        {{ Form::text('email', Input::old('email'),
                        array('id'=>'email','class'=>'form-control','placeholder' => 'E-mail')) }}
                    </div>
                    <label for="password" class="sr-only">Password</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                        {{ Form::password('password', array('id'=>'password','class'=>'form-control','placeholder' => 'Password')) }}
                    </div>
                    <label for="password_confirmation" class="sr-only">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                        {{ Form::password('password_confirmation', array('id'=>'password_confirmation','class'=>'form-control','placeholder' => 'Confirm Password')) }}
                    </div>
                    <label for="captcha" class="sr-only">Are you a robot?</label>
                          <div class="form-horizontal input-group">
                                  <span class="input-group-addon" style="padding:0">{{HTML::image(Captcha::img(), 'This is a Captcha image, please enable images to complete this form')}}</span>
                                  {{ Form::text('captcha',null,array('id'=>'captcha','class'=>'form-control','placeholder' => 'Not case sensitive!')) }}
                          </div>
                    <p>

                        {{Form::submit('Register', array('name'=>'register', 'value'=>'Register', 'class'=>'pure-button pure-button'));}}
                    </p>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <div class="pure-u-1 pure-u-md-1-3">
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        $('form').validate();
    </script>
@endsection
