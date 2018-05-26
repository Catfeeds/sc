<?php

/**
* 图片
*/
Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    Route::get('gallery/images/{gallery_id}', 'ImageController@index');
    Route::get('images/{gallery_id}/table', 'ImageController@table');
    Route::post('images/state', 'ImageController@state');
    Route::get('images/{gallery_id}/sort', 'ImageController@sort');
    Route::get('images/comments/{id}','ImageController@comments');
    Route::get('images/{gallery_id}/create', 'ImageController@create');
    Route::post('images/{gallery_id}', 'ImageController@store');
    Route::post('images/{id}/save', 'ImageController@save');
    Route::post('images/{id}/state', 'ImageController@state');
    Route::post('images/{id}/top', 'ImageController@top');
    Route::post('images/{id}/tag', 'ImageController@tag');
    Route::resource('images', 'ImageController');
});
