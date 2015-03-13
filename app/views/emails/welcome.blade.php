<h1>Hi, {{$username}}!</h1>
<p>In order to validate your account with the username: {{$username}}, please enter the following key on the validation page: {{$key}}</p>
<p>Alternatively, please click {{ HTML::linkRoute('verify.email', 'here', ['key'=>$key]) }} to automatically verify your account. </p>
<p>We'd like to personally welcome you to Sitting Services. Thank you for registering!</p>