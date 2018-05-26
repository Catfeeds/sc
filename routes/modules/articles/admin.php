<?php

/**
* 文章
*/
Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    Route::get('articles/table', 'ArticleController@table');
    Route::post('articles/state', 'ArticleController@state');
    Route::get('articles/sort', 'ArticleController@sort');
    Route::get('articles/categories', 'ArticleController@categories');
    Route::post('articles/{id}/save', 'ArticleController@save');
    Route::post('articles/{id}/top', 'ArticleController@top');
    Route::resource('articles', 'ArticleController');
});
