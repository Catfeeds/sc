<?php

/**
* 图集
*/
Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    Route::get('galleries/table', 'GalleryController@table');
    Route::post('galleries/state', 'GalleryController@state');
    Route::get('galleries/sort', 'GalleryController@sort');
    Route::get('galleries/comments/{id}','GalleryController@comments');
    Route::get('galleries/categories', 'GalleryController@categories');
    Route::post('galleries/{id}/save', 'GalleryController@save');
    Route::post('galleries/{id}/top', 'GalleryController@top');
    Route::post('galleries/{id}/tag', 'GalleryController@tag');
    Route::post('galleries/batch/import', 'GalleryController@import');
    Route::get('galleries/image/update', 'GalleryController@updateItem');
    Route::resource('galleries', 'GalleryController');
});
