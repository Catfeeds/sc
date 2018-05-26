<?php

/**
* 线下课程
*/
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {

    $api->group(['namespace' => 'App\Api\Controllers'], function ($api) {
        $api->get('offlinecourses', 'OfflineCourseController@lists');
        $api->get('offlinecourses/search', 'OfflineCourseController@search');
        $api->get('offlinecourses/info', 'OfflineCourseController@info');
        $api->get('offlinecourses/detail', 'OfflineCourseController@detail');
        $api->get('offlinecourses/share', 'OfflineCourseController@share');
    });
});