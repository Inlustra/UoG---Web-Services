@extends('layouts.main')

@section('content')
    <div class="pure-u-1 pure-u-md-1-3">
    </div>
    <div class="pure-u-1 pure-u-md-1-3 fill">
        <div class="center-wrapper">
            <div class="login-box container">
                {{ Form::open(array('url' => 'user/auth', 'class'=>'pure-form pure-g')) }}
                    <!-- if there are login errors, show them here -->
                    @if(Session::has('errors'))
                    <div class="pure-alert pure-alert-error">
                        <span class="sr-only">Error:</span>
                        <i class="fa fa-exclamation-circle"></i>
                        {{ $errors->first('username') }}
                        {{ $errors->first('password') }}
                    </div>
                    @endif
                    <p>
                        <label for="username" class="sr-only">Username</label>
                        <div class="input-group">

                            <span class="input-group-addon"><i class="fa fa-user"></i></span>
                            {{ Form::text('username', Input::old('username') == null ? Cookie::get('username') : "",
                            array('id'=>'username','class'=>'form-control','placeholder' => 'Username')) }}
                        </div>
                    </p>

                    <p>
                        <label for="password" class="sr-only">Password</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                            {{ Form::password('password', array('id'=>'password','class'=>'form-control','placeholder' => 'Password')) }}
                        </div>
                    </p>
                    <div style="float:left;">
                            <input type="checkbox" name="remember" value="1"
                            @if(Cookie::get('cookieconsent') != 1)
                            disabled="disabled"
                            @endif
                            /> <label for="remember">Remember me</label>
                    </div>
                    <div style="float:right;">
                        {{Form::submit('Login', array('name'=>'login', 'value'=>'Login', 'class'=>'pure-button pure-button-primary'));}}
                        {{ HTML::linkRoute('user.register', 'Register', array(), array('class'=>'pure-button pure-button')) }}
                    </div>
                    <div style="clear: both;"></div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <div class="pure-u-1 pure-u-md-1-3">
    </div>
    @if(Cookie::get('cookieconsent') != 1)
    <div class="footer">
         <span>This website uses cookies to store information on your computer, in order to remember you next time you login.</span>
         <span> <a href="{{URL::to('user/cookieconsent')}}"><button class="pure-button pure-button-primary">Accept all cookie for this site</button></a></span>
     </div>
     @endif
@stop

@section('scripts')
    <script type="text/javascript">
        $('form').validate();
    </script>
@endsection