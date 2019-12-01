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
    Route::get('/', function () {
        return ([
            'gitHubSources' => 'https://github.com/OpenEpicData/GamerClubAPI'
        ]);
    });

    Route::group(['prefix' => 'article'], function () {
        Route::resource('fetch', 'Article\FetchController');
        Route::resource('news', 'Article\NewsController');
        Route::resource('refs', 'Article\RefController');
        Route::resource('tags', 'Article\TagController');
    });

    Route::group(['prefix' => 'analysis'], function () {
        Route::resource('news', 'Analysis\NewsController');
    });

    Route::group(['prefix' => 'game'], function () {
        Route::group(['prefix' => 'steam'], function () {
            Route::resource('fetch_user_count', 'Game\Steam\FetchUserCountController');
            Route::resource('fetch_weekly_top_sellers', 'Game\Steam\FetchWeeklyTopSellersController');
            Route::resource('user_count', 'Game\Steam\UserCountController');
            Route::resource('weekly_top_sellers', 'Game\Steam\WeeklyTopSellersController');

            Route::group(['prefix' => 'spider'], function () {
                Route::get('app', 'Game\Steam\Spider\AppController@index');
            });
        });
    });
});
