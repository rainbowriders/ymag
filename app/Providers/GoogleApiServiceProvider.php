<?php

namespace App\Providers;

use App\User;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Gmail;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\ServiceProvider;

class GoogleApiServiceProvider extends ServiceProvider
{
    protected function initClient()
    {
        $client = new Google_Client;

        $client->setScopes(config('services.google.scopes'));
        $client->setApplicationName(config('app.url'));
        $client->setAuthConfigFile(resource_path('client_secret.json'));
        $client->setApprovalPrompt('force');    

        $client->setAccessType('offline');

        return $client;
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('google.client', function () {
            $client = $this->initClient();

            // @todo: Remove this statement on production
            //$client->setApprovalPrompt('force');
//            if (env('FORCE_ACCOUNT_CHOOSER', true)) {
//                $client->setPrompt('select_account');
//            }

            /**
             * @var Guard $auth;
             */
            if (($auth = auth()) && $auth->check()) {
                $client = $this->handleRefreshToken($auth, $client);
            }

            return $client;
        });

        $this->app->bind('google.client.api', function () {
            $client = $this->initClient();

            /**
             * @var Guard $auth;
             */
            if (($auth = auth('api')) && $auth->check()) {
                $client = $this->handleRefreshToken($auth, $client);
            }

            return $client;
        });

        $this->app->bind('google.calendar', function ($app) {
            return $calendar = new Google_Service_Calendar(
                $app['google.client']
            );
        });

        $this->app->bind('google.calendar.api', function ($app) {
            return $calendar = new Google_Service_Calendar(
                $app['google.client.api']
            );
        });

        $this->app->bind('google.mail', function ($app) {
            return new Google_Service_Gmail(
                $app['google.client']
            );
        });

        $this->app->bind('google.mail.api', function ($app) {
            return new Google_Service_Gmail(
                $app['google.client.api']
            );
        });
    }

    /**
     * @param Guard $auth
     * @param Google_Client $client
     */
    protected function handleRefreshToken($auth, $client)
    {
        /**
         * @var $user User
         */
        $user = $auth->user();
        $token = $user->token;

        $client->setAccessToken(json_encode($token));

        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            $client->refreshToken(
                $refreshToken = $client->getRefreshToken()
            );

            $user->refreshToken($refreshToken);
        }

        return $client;
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
