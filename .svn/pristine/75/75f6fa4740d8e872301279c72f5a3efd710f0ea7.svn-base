<?php

/**
* 图集
*/
Route::group(['middleware' => 'web'], function () {
    Route::get('galleries/index.html', 'GalleryController@lists');
    Route::get('galleries/category-{id}.html', 'GalleryController@category');
    Route::get('galleries/detail-{id}.html', 'GalleryController@show');
    Route::get('galleries/{slug}.html', 'GalleryController@slug');
});
