@extends('layouts.main')

@section('content')
    <div class="pure-u-1 pure-u-md-1-3">
    </div>
    <div class="pure-u-1 pure-u-md-1-3">
        <div class="center-wrapper">
            <div class="login-box container">
                {{ Form::open(array('route' => 'verify.post')) }}
                		<h1>Hi {{is_null(Auth::user()->name) ? Auth::user()->name : Auth::user()->username}}!</h1>
                		<h2>Please verify your account</h2>
                        <p>This key was sent to the e-mail address: {{Auth::user()->email}}</p>
                		<!-- if there are login errors, show them here -->
                		<p>
                			{{ $errors->first('key') }}
                		</p>
                        <label for="key" class="sr-only">Verification key:</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-key"></i></span>
                            {{ Form::text('key', null, array('id'=>'key','class'=>'form-control','placeholder' => 'Verification code...')) }}
                        </div>
                        </br>
                        {{Form::submit('Verify Me!', array('class'=>'pure-button pure-button'));}}
                	{{ Form::close() }}

                    <p>Please click {{ HTML::linkRoute('resend', 'here') }} to resend the e-mail.</p>
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
