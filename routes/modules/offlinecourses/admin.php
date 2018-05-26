<?php

/**
* 线下课程
*/
Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    Route::get('offlinecourses/table', 'OfflineCourseController@table');
    Route::post('offlinecourses/state', 'OfflineCourseController@state');
    Route::get('offlinecourses/sort', 'OfflineCourseController@sort');
    Route::get('offlinecourses/comments/{id}','OfflineCourseController@comments');
    Route::post('offlinecourses/{id}/save', 'OfflineCourseController@save');
    Route::post('offlinecourses/{id}/top', 'OfflineCourseController@top');
    Route::post('offlinecourses/{id}/tag', 'OfflineCourseController@tag');
    Route::resource('offlinecourses', 'OfflineCourseController');
});
