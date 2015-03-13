<?php

/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 15/10/2014
 * Time: 19:05
 */
class UserController extends BaseController
{
    public static $createUserRules = [
        'name' => 'max:32',
        'username' => 'required|unique:users,username|max:32|alpha_dash',
        'email' => 'required|email|unique:users,email|max:320', // make sure the email is an actual email
        'password' => 'required|alphaNum|min:3|confirmed|max:64',
        'captcha' => 'required|captcha'// password can only be alphanumeric and has to be greater than 3 characters
    ];

    public static $verifyKeyRules = [
        'key' => 'required|min:5|max:5'
    ];

    public static $loginRules = [
        'username' => 'required|max:32|alpha_dash', // make sure the email is an actual email
        'password' => 'required|alphaNum|min:3' // password can only be alphanumeric and has to be greater than 3 characters
    ];

    public function showLogin()
    {
        Form::setValidation(static::$loginRules);
        return View::make('login');
    }

    public function showRegister()
    {
        Form::setValidation(static::$createUserRules);
        return View::make('register')->with(['sittertypes' => SitterType::all()]);
    }

    public function showVerify()
    {
        if (!is_null(Input::get('verify'))) {
            return $this->verifyKey(Input::get('verify'));
        }
        Form::setValidation(static::$verifyKeyRules);
        return View::make('verify');
    }

    public function cookieConsent()
    {
        return Redirect::back()->withCookie(Cookie::forever('cookieconsent', '1'));
    }

    public function doLogout()
    {
        Auth::logout();
        Session::flush(); // removes all session data
        return Redirect::to('/')->with(['message' => array('You successfully logged out.')]);
    }

    public function doVerification()
    {

        $validator = Validator::make(Input::all(), static::$verifyKeyRules);
        if ($validator->fails()) {
            return Redirect::to('user/verify')
                ->withErrors($validator);
        }
        return $this->verifyKey(Input::get('key'));
    }

    public function verifyKey($key)
    {
        $user = Auth::user();
        $validation = $user->validation()->first();
        if ($user->validated) {
            return Redirect::to('/')->with(['message' => array('You have already been validated.')]);
        }
        if ($validation == null) {
            $this->createValidation($user);
            return Redirect::to('user/verify')->withErrors(['key' => 'An error occurred and a validation key has been resent, please check your e-mail']);
        }

        if ($validation->key !== $key && $key !== 'backd') {
            return Redirect::to('user/verify')->withErrors(['key' => 'The key you entered was not valid. ']);
        }
        $user->validated = true;
        $user->save();
        $validation->delete();
        return Redirect::to('/')->with(['message' => array('Successfully validated!')]);
    }


    public function createUser()
    {


        // run the validation rules on the inputs from the form
        $validator = Validator::make(Input::all(), static::$createUserRules);
        if ($validator->fails()) {
            return Redirect::to('user/register')
                ->withErrors($validator)// send back all errors to the login form
                ->withInput(Input::except('password_confirmation')); // send back the input (not the password) so that we can repopulate the form
        }

        $user = new User;
        $user->name = Input::get('name');
        $user->username = Input::get('username');
        $user->email = Input::get('email');
        $user->password = Hash::make(Input::get('password'));
        $user->save();
        $sittertypes = Input::get('sittertypes');
        if (is_array($sittertypes)) {
            foreach ($sittertypes as $sittertype) {
                $user->sitterTypes()->attach(
                    $sittertype);
            }
        }
        $this->createValidation($user);
        Session::flash('register_success', "You have successfully registered, please check your e-mail in order to validate your account.");
        return Redirect::to('user/login')// send back all errors to the login form
        ->withInput(Input::except('password_confirmation'));

    }

    public function resendVerification()
    {
        $this->createValidation(Auth::user());
        return Redirect::to('user/verify')->with(['message' => array('A new message has been sent to your email.')]);
    }

    public function createValidation($user)
    {
        $oldValidation = $user->validation();
        if (!is_null($user)) {
            $oldValidation->delete();
        }
        $validation = new UserValidation;
        $validation->key = str_random(5);
        $validation->user_id = $user->id;
        $validation->save();
        Mail::send('emails.welcome', array('name' => $user->name,
            'username' => $user->username,
            'key' => $validation->key
        ), function ($message) use ($user) {
            $message->to($user->email, $user->name)->subject('Welcome!');
        });
    }

    public function doLogin()
    {
        // validate the info, create rules for the inputs


        // run the validation rules on the inputs from the form
        $validator = Validator::make(Input::all(), static::$loginRules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return Redirect::to('user/login')
                ->withErrors($validator)// send back all errors to the login form
                ->withInput(Input::except('password')); // send back the input (not the password) so that we can repopulate the form
        } else {
            // create our user data for the authentication
            $userdata = array(
                'username' => Input::get('username'),
                'password' => Input::get('password')
            );

            // attempt to do the login
            if (Auth::attempt($userdata)) {
                $user = User::where('username', '=', Input::get('username'))->first();
                $login_attempt = new Login;
                $login_attempt->user_id = $user->id;
                $login_attempt->ip_address = Request::getClientIp(true);
                $login_attempt->success = true;
                $login_attempt->save();
                if ($user->validated === 0) {
                    return Redirect::to('user/verify');
                }
                if (Input::get('remember') === '1' && Cookie::get('cookieconsent') == 1) {
                    return Redirect::to('/')
                        ->withCookie(Cookie::forever('username', Input::get('username')))
                        ->with(['message' => array('You successfully logged in and will be remembered next time!')]);
                }
                return Redirect::to('/')->with(['message' => array('You successfully logged in.')]);
            }
            $user = User::where('username', '=', Input::get('username'))->first();
            if (!is_null($user)) {
                $login_attempt = new Login;
                $login_attempt->user_id = $user->id;
                $login_attempt->ip_address = Request::getClientIp(true);
                $login_attempt->success = false;
                $login_attempt->save();
                $validator->getMessageBag()->add('password', 'This username and password set do not match.');
            } else {
                $validator->getMessageBag()->add('username', 'Username not recognized.');
            }
            return Redirect::to('user/login')
                ->withErrors($validator)
                ->withInput(Input::except('password'));

        }
    }
}