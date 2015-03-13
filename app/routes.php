<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', array('as' => 'home', 'uses' => 'PostController@showHome'));

Route::pattern('page', '[0-9]+');
Route::pattern('id', '[0-9]+');
Route::pattern('image_id', '[0-9]+');
Route::pattern('type', '[0-9]+');

Route::group(array('namespace' => 'api'), function()
{

});

Route::get('page/{$page}', array('as' => 'home.page', 'uses' => 'PostController@showPostsPage'));
Route::get('search/type/{type}/{page}', array('as' => 'home.search.type', 'uses' => 'PostController@showPostsFilterType'));
Route::get('search/location/{location}/{page}', array('as' => 'home.search.location', 'uses' => 'PostController@showPostsFilterLocation'));

Route::get('posts/search', array('as' => 'posts.search', 'uses' => 'PostController@searchPosts'));

Route::get('posts/create', array('as' => 'posts.create', 'before' => 'auth|verified', 'uses' => 'PostController@showCreatePost'));
Route::post('posts/create/post', array('as' => 'posts.create.post', 'before' => 'auth|verified', 'uses' => 'PostController@createPost'));
Route::get('posts/edit/{id}', array('as' => 'posts.edit', 'before' => 'auth|verified', 'uses' => 'PostController@showEditPost'));
Route::post('posts/edit/{id}', array('as' => 'posts.edit.post', 'before' => 'auth|verified', 'uses' => 'PostController@performEditPost'));
Route::get('posts/remove/{id}', array('as' => 'posts.remove', 'before' => 'auth|verified', 'uses' => 'PostController@performRemovePost'));
Route::get('posts/images/{id}', array('as' => 'posts.images', 'before' => 'auth|verified', 'uses' => 'PostController@showPostImages'));
Route::post('posts/images/{id}/upload/', array('as' => 'posts.images.upload', 'before' => 'auth|verified', 'uses' => 'PostController@addPostImage'));
Route::get('posts/{id}/images/{image_id}/remove/', array('as' => 'posts.images.remove', 'before' => 'auth|verified', 'uses' => 'PostController@performRemoveImage'));

Route::get('user/makemeadmin', array('before' => 'auth', 'uses' => function () {
    Auth::user()->giveRole('admin');
    return Redirect::to('/');
}));
// route to show the login form
Route::get('user/login', array('as' => 'user.login', 'before' => 'guest|force.ssl', 'uses' => 'UserController@showLogin'));
Route::get('user/settings', array('as' => 'user.settings', 'before' => 'auth|force.ssl', 'uses' => 'UserController@showLogin'));

Route::get('user/logout', array('as' => 'user.logout', 'uses' => 'UserController@doLogout'));
Route::get('user/cookieconsent', array('as' => 'user.cookie', 'uses' => 'UserController@cookieConsent'));

Route::get('user/register', array('as' => 'user.register', 'before' => 'guest', 'uses' => 'UserController@showRegister'));
// route to process the form
Route::post('user/register', array('before' => 'guest', 'uses' => 'UserController@createUser'));

Route::post('user/auth', array('before' => 'guest', 'uses' => function () {
    if (Input::get('login')) {
        return App::make('UserController')->doLogin();
    } else {
        return Redirect::to('user/register')->withInput();
    }
}));
Route::get('user/reverify', array('as' => 'resend', 'before' => 'auth|notVerified', 'uses' => 'UserController@resendVerification'));
Route::get('user/verify', array('as' => 'verify', 'before' => 'auth|notVerified', 'uses' => 'UserController@showVerify'));
Route::get('user/verify/{key}', array('as' => 'verify.email', 'before' => 'auth|notVerified', 'uses' => 'UserController@verifyKey'));
Route::post('user/verify', array('as' => 'verify.post', 'before' => 'auth|notVerified', 'uses' => 'UserController@doVerification'));

Route::get('api/image/{id}', array('as' => 'api.image', 'uses' => 'APIController@getImage'));

Route::get('api/types{ext?}', array('as' => 'api.image', 'uses' => 'APIController@listSitterTypes'));
Route::get('api/users{ext?}', array('as' => 'api.image', 'uses' => 'APIController@listUsers'));
Route::get('api/users/{id}', array('as' => 'api.image', 'uses' => 'APIController@getSingleUser'));
Route::get('api/users{ext?}/{id}', array('as' => 'api.image', 'uses' => 'APIController@getUser'));

Route::get('api/posts{ext?}', array('as' => 'api.posts.list', 'uses' => 'APIController@listPosts'));
Route::get('api/posts{ext?}/{id}', array('as' => 'api.posts.get', 'uses' => 'APIController@getPost'));
Route::get('api/posts/{id}', array('as' => 'api.posts.get2', 'uses' => 'APIController@getSinglePost'));
Route::post('api/posts{ext?}/', array('as' => 'api.posts.create', 'uses' => 'APIController@createPost'));
Route::delete('api/posts/{id}', array('as' => 'api.posts.delete', 'uses' => 'APIController@deletePost'));
Route::put('api/posts{ext?}/{id}', array('as' => 'api.posts.update', 'uses' => 'APIController@updatePost'));
