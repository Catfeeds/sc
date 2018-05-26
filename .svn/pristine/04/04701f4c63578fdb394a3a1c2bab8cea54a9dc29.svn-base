<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('register', 'Member\RegisterController@showRegistrationForm');
Route::post('register', 'Member\RegisterController@register');
Route::get('login', 'Member\LoginController@showLoginForm');
Route::post('login', 'Member\LoginController@login');
Route::get('logout', 'Member\LoginController@logout');
// Password Reset Routes...
Route::get('member/verify', 'MemberController@showVerify');
Route::get('phone/login', 'MemberController@phoneLogin');
Route::post('phone/login', 'Member\LoginController@login');
Route::post('password/email', 'Member\ForgotPasswordController@sendResetLinkEmail');

Route::group(['middleware' => 'web'], function () {

    //图库作者获取


});
