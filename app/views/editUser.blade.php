<!doctype html>
<html>
<head>
	<title>Edit user details</title>
</head>
<body>
    @if (Session::has('message'))
       <p>{{ Session::get('message') }}</p>
    @endif
	{{ Form::open(array('url' => 'user/auth')) }}
		<h1>Login</h1>

		<!-- if there are login errors, show them here -->
		<p>
			{{ $errors->first('email') }}
			{{ $errors->first('password') }}
		</p>

		<p>
			{{ Form::label('email', 'Email Address') }}
			{{ Form::text('email', Input::old('email'), array('placeholder' => 'awesome@awesome.com')) }}
		</p>

		<p>
			{{ Form::label('password', 'Password') }}
			{{ Form::password('password') }}
		</p>

        <input type="submit" name="login" value="Login">
        <input type="submit" name="register" value="Register">
	{{ Form::close() }}

</body>
</html>