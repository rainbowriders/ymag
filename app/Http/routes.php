<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

use App\Repositories\GMailRepository;

Route::group(['middleware' => 'web'], function () {
    Route::get('/', ['as' => 'privacy', 'uses' => function () {
        return view('privacy');
    }]);

    Route::get('/', [
        'as'         => 'dashboard',
        'middleware' => 'auth',
        'uses'       => 'DashboardController@index',
    ]);

    Route::get('login', ['middleware' => 'guest', 'uses' => function () {
        return view('login');
    }]);

    Route::get('logout', [
        'as'         => 'logout',
        'middleware' => 'auth',
        'uses'       => 'GoogleController@logout',
    ]);

    Route::get('flush', [
        'as'         => 'flush',
        'middleware' => 'auth',
        'uses'       => 'GoogleController@flush',
    ]);

    Route::group(['prefix' => 'prefs', 'middleware' => 'auth'], function () {
        Route::get('lang/{language}', [
            'as'   => 'user.prefs.lang',
            'uses' => function ($language) {
                /**
                 * @var $user \App\User
                 */
                $user = auth()->user();
                $user->savePreference('language', $language);

                return redirect()->back(301);
            },
        ]);

        Route::get('layout/{id}', [
            'as'   => 'user.prefs.layout',
            'uses' => function ($layout) {
                /**
                 * @var $user \App\User
                 */
                $user = auth()->user();
                $user->savePreference('theme', $layout);

                return redirect()->back(301);
            },
        ]);
    });

    Route::group([
        'middleware' => 'guest',
        'prefix'     => 'auth/google',
    ], function () {
        Route::get('/', [
            'as'   => 'google.request-token',
            'uses' => 'GoogleController@requestToken',
        ]);

        Route::get('callback', [
            'as'   => 'google.request-user',
            'uses' => 'GoogleController@requestUser',
        ]);
    });

    Route::group([
        'prefix'     => 'api/v1',
        'middleware' => 'auth',
    ], function () {
        Route::get('gmail/messages/{id}/body', [
            'as'   => 'gmail.body',
            'uses' => function ($messageId, GMailRepository $repo) {
                $me = auth()->user();

                $body = $repo->get($me->email, $messageId)->body();

                return view('iframe', [
                    'body' => array_get($body, 'html', array_get($body, 'plain')),
                ]);
            },
        ]);

        Route::get('gmail/messages/{message_id}/attachment/{attachment_id}', [
            'as'   => 'gmail.attachment',
            'uses' => function ($messageId, $attachmentId, GMailRepository $repo) {
                $me = auth()->user();

                $data = $repo->fetchAttachment($me->email, $messageId, $attachmentId);

                return response(\App\Base64::decode($data), 200);
            },
        ]);
    });
});

Route::group([
    'prefix'     => 'api/v1',
    'middleware' => 'auth:api',
], function () {
    Route::get('quotes/random', [
        'as'   => 'api.quotes.random',
        'uses' => 'Api\QuotesController@random',
    ]);

    Route::group(['prefix' => 'gmail'], function () {
        Route::get('labels', [
            'as'   => 'api.gmail.labels',
            'uses' => 'Api\GmailController@labels',
        ]);

        Route::get('messages', [
            'as'   => 'api.gmail.messages',
            'uses' => 'Api\GmailController@lists',
        ]);

        Route::get('messages/{id}', [
            'as'   => 'api.gmail.message',
            'uses' => 'Api\GmailController@get',
        ]);

        Route::get('messages/{id}/touch', [
            'as'   => 'api.gmail.message.touch',
            'uses' => 'Api\GmailController@touch',
        ]);
    });

    Route::get('weather/get', [
        'as'   => 'api.weather.proxy',
        'uses' => 'Api\WeatherController@get',
    ]);

    Route::get('feed/news', [
        'as'   => 'api.feed.news',
        'uses' => 'Api\FeedController@news',
    ]);
    Route::post('feed', [
        'as'   => 'api.feed',
        'uses' => 'Api\FeedController@postFeed',
    ]);
    Route::delete('feed/{id}', [
        'as'   => 'api.feed.delete',
        'uses' => 'Api\FeedController@deleteFeed',
    ]);

    Route::group(['prefix' => 'calendar'], function () {
        Route::get('list', [
            'as'   => 'api.calendar.list',
            'uses' => 'Api\CalendarController@calendars',
        ]);

        Route::get('events', [
            'as'   => 'api.calendar.events',
            'uses' => 'Api\CalendarController@events',
        ]);
    });

    Route::group(['prefix' => 'geo'], function () {
        Route::get('ip', [
            'as'   => 'api.geo.geoip',
            'uses' => 'Api\GeoController@geoip',
        ]);

        Route::get('code', [
            'as'   => 'api.geo.code',
            'uses' => 'Api\GeoController@code',
        ]);

        Route::get('lookup', [
            'as'   => 'api.geo.lookup',
            'uses' => 'Api\GeoController@lookup',
        ]);

        Route::get('places', [
            'as'   => 'api.geo.place',
            'uses' => 'Api\GeoController@places',
        ]);
    });

    Route::group(['prefix' => 'custom-event'], function () {
        Route::get('/', [
            'as'   => 'custom.event',
            'uses' => 'Api\CustomEventController@getCustomEvent',
        ]);

        Route::post('/', [
            'as'   => 'custom.event',
            'uses' => 'Api\CustomEventController@postCustomEvent',
        ]);
        Route::post('/{id}', [
            'as'   => 'custom.event',
            'uses' => 'Api\CustomEventController@updateCustomEvent',
        ]);
    });
});
