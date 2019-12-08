<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'api'], function () {
    Route::get('/', fn() => ([
        'gitHubSources' => 'https://github.com/OpenEpicData/GamerClubAPI'
    ]));

    Route::get('queue/app', 'Queue\AppController@index');

    Route::group(['prefix' => 'article', 'namespace' => 'Article'], function () {
        Route::resource('news', 'NewsController');
        Route::resource('refs', 'RefController');
        Route::resource('tags', 'TagController');
    });

    Route::group(['prefix' => 'analysis', 'namespace' => 'Analysis'], function () {
        Route::resource('news', 'NewsController');
    });

    Route::group(['prefix' => 'game', 'namespace' => 'Game'], function () {
        Route::group(['prefix' => 'steam', 'namespace' => 'Steam'], function () {
            Route::resource('status', 'UserCountController');
            Route::resource('weeklyTopSellers', 'WeeklyTopSellersController');
            Route::resource('apps', 'AppController');
        });
    });

    /**
     * 爬虫模块
     */
    Route::group(['prefix' => 'spider', 'namespace' => 'Spider'], function () {
        Route::group(['prefix' => 'article', 'namespace' => 'Article'], function () {
            Route::get('news', 'NewsController@index');
        });

        Route::group(['prefix' => 'steam', 'namespace' => 'Steam'], function () {
            Route::get('apps', 'AppController@index');
            Route::get('status', 'StatusController@index');
            Route::get('weeklyTopSellers', 'WeeklyTopSellersController@index');
        });
    });
});
