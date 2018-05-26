<?php

/**
* 图片
*/
Route::group(['middleware' => 'web'], function () {
    Route::get('images/index.html', 'ImageController@lists');
    Route::get('images/detail-{id}.html', 'ImageController@show');
    Route::get('images/{slug}.html', 'ImageController@slug');
});
