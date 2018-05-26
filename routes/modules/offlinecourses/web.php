<?php

/**
* 线下课程
*/
Route::group(['middleware' => 'web'], function () {
    Route::get('offlinecourses/index.html', 'OfflineCourseController@lists');
    Route::get('offlinecourses/detail-{id}.html', 'OfflineCourseController@show');
    Route::get('offlinecourses/{slug}.html', 'OfflineCourseController@slug');
});
