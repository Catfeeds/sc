<?php

Route::group(['prefix' => 'admin'], function () {
    Route::get('login', 'AdminController@login');
    Route::get('logout', 'Auth\LoginController@logout');
    Route::post('login', 'Auth\LoginController@login');
    Route::get('captcha', 'Auth\LoginController@captcha');
});

Route::group(['prefix' => 'admin', 'middleware' => 'auth.admin'], function () {

    Route::get('/', 'AdminController@dashboard');
    Route::get('/index', 'AdminController@dashboard');

    /**
     * 学校
     */
   Route::get('schools/table', 'SchoolController@table');
   Route::post('schools/state', 'SchoolController@state');
   Route::post('schools/{id}/save', 'SchoolController@save');
   Route::resource('schools', 'SchoolController');

    /**
     * 年级班级
     */
    Route::get('grades/table', 'GradeController@table');
    Route::post('grades/state', 'GradeController@state');
    Route::post('grades/{id}/save', 'GradeController@save');
    Route::resource('grades', 'GradeController');


    /**
     * 学生
     */
    Route::get('students/table', 'StudentController@table');
    Route::post('students/state', 'StudentController@state');
    Route::post('students/{id}/save', 'StudentController@save');
    Route::resource('students', 'StudentController');








    /**
     * 角色管理
     */
    Route::get('roles/table', 'RoleController@table');
    Route::resource('roles', 'RoleController');
    Route::get('roles/{id}/delete', 'RoleController@destroy');



    /**
     * 首页管理
     */
    Route::get('pics/table', 'PicController@table');
    Route::post('pics/state', 'PicController@state');
    Route::get('pics/sort', 'PicController@sort');
    Route::get('pics/categories', 'PicController@categories');
    Route::post('pics/{id}/save', 'PicController@save');
    Route::resource('pics', 'PicController');

    /**
     * 优惠券管理
     */
    Route::get('coupons/table', 'CouponController@table');
    Route::post('coupons/state', 'CouponController@state');
    Route::get('coupons/sort', 'CouponController@sort');
    Route::get('coupons/categories', 'CouponController@categories');
    Route::post('coupons/{id}/save', 'CouponController@save');
    Route::resource('coupons', 'CouponController');

    /**
     * 活动管理
     */
    Route::get('activities/table', 'ActivityController@table');
    Route::post('activities/state', 'ActivityController@state');
    Route::get('activities/sort', 'ActivityController@sort');
    Route::post('activities/{id}/save', 'ActivityController@save');
    Route::resource('activities', 'ActivityController');

    /**
     * 订单日志
     */
    Route::get('orders/logs', 'AdminController@orderLog');
    Route::get('orders/logs/table', 'AdminController@orderLogTable');
    /**
     * 订单管理
     */
    Route::get('orders/table', 'OrderController@table');
    Route::post('orders/state', 'OrderController@state');
    Route::get('orders/sort', 'OrderController@sort');
    Route::post('orders/{id}/save', 'OrderController@save');
    Route::resource('orders', 'OrderController');


    /**
     * 获取oss签名
     */
    Route::get('oss/signature', 'OssUploadController@signature');

    Route::get('subname/lists', 'HomeController@subnames');

});