<?php

/**
* 图集
*/
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {

    $api->group(['namespace' => 'App\Api\Controllers'], function ($api) {
        $api->get('galleries', 'GalleryController@lists');
        $api->get('galleries/search', 'GalleryController@search');
        $api->get('galleries/info', 'GalleryController@info');
        $api->get('galleries/detail', 'GalleryController@detail');
        $api->get('galleries/share', 'GalleryController@share');
    });
});