<?php

/**
* 图片
*/
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {

    $api->group(['namespace' => 'App\Api\Controllers'], function ($api) {
        $api->get('images', 'ImageController@lists');
        $api->get('images/search', 'ImageController@search');
        $api->get('images/info', 'ImageController@info');
        $api->get('images/detail', 'ImageController@detail');
        $api->get('images/share', 'ImageController@share');
    });
});