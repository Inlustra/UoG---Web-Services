<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function ($request) {
    Validator::extend('postcode', function ($attribute, $value) {
        $postcode = strtoupper(str_replace(' ', '', $value));
        return (preg_match("/^[A-Z]{1,2}[0-9]{2,3}[A-Z]{2}$/", $postcode)
            || preg_match("/^[A-Z]{1,2}[0-9]{1}[A-Z]{1}[0-9]{1}[A-Z]{2}$/", $postcode)
            || preg_match("/^GIR0[A-Z]{2}$/", $postcode));
    });
    Validator::extend('greenwich', function ($attribute, $value, $parameters) {
        $val = strtolower($value);
        $valids = array('se9', 'se2', 'se18', 'se28', 'se7', 'se3', 'se10', 'se12', 'se13', 'se8');
        foreach ($valids as $start) {
            if (substr($val, 0, strlen($start)) === $start)
                return true;
        }
        return false;
    });
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
    header('Access-Control-Allow-Credentials: true');
});



App::after(function ($request, $response) {
    //
});
Route::filter('force.ssl', function () {
    if (!Request::secure()) {
        return Redirect::secure(Request::path());
    }
});


Route::filter('admin', function () {
    if (!Auth::check()) {
        return Redirect::to('login')->with(['message' => array('You must be logged in before you can access this page.')]);
    }
    if (!Auth::user()->hasRole('admin')) {
        return Redirect::back();

    }
});

//Filter for login pages, redirect them to front page if already logged in.
Route::filter('guest', function () {
    if (Auth::check()) {
        return Redirect::to('/')->with(['message' => array('You\'re already logged in!')]);
    }
});

//Filter for pages such as make post and comment
Route::filter('verified', function () {
    if (!Auth::user()->validated) {
        return Redirect::to('user/verify')->with(['message' => array('You must be verified before you can access this page.')]);
    }
});

//Filter for pages such as make post and comment
Route::filter('notVerified', function () {
    if (Auth::user()->validated) {
        return Redirect::to('/')->with(['message' => array('You are already verified.')]);
    }
});

//Filter for pages requiring just a log in such as settings
Route::filter('auth', function () {
    if (!Auth::check()) {
        return Redirect::to('user/login')->with('message', array('You must be logged in before you can access this page.'));
    }
});
/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function () {
    if (Auth::guest()) {
        if (Request::ajax()) {
            return Response::make('Unauthorized', 401);
        } else {
            return Redirect::guest('login');
        }
    }
});


Route::filter('auth.basic', function () {
    return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function () {
    if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function () {
    if (Session::token() != Input::get('_token')) {
        throw new Illuminate\Session\TokenMismatchException;
    }
});
