<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\User;
use Auth;
use Google_Auth_OAuth2;
use Google_Service_Plus;
use Guzzle\Http\Client;
use Illuminate\Http\Request;
use Session;
use App\NewsFeed;

class GoogleController extends Controller
{
    public function requestToken()
    {
        $client = app('google.client');

        $auth = new Google_Auth_OAuth2($client);

        $url = $auth->createAuthUrl(
            implode(' ', config('services.google.scopes'))
        );

        return redirect()->to($url);
    }

    public function requestUser(Request $request)
    {
        try {
            $client = app('google.client');

            $client->authenticate(
                $request->get('code')
            );

            $plus = new Google_Service_Plus($client);

            $user = User::fromGPlusUser(
                $me = $plus->people->get('me'),
                $client->getAccessToken()
            );

            $this->login($user);

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([trans('auth.unable_to_fetch')]);
        }
    }

    public function logout()
    {
        if (env('FORCE_GOOGLE_LOGOUT', false)) {
            return redirect()->to('https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue=' . url('flush'));
        }

        return $this->flush();
    }

    public function flush()
    {
        Auth::guard()->logout();

        Session::flush();

        return redirect()->to('login');
    }

    /**
     * @param $user
     */
    protected function login($user)
    {
        Auth::login($user, true);

        // build the token
        $user->fill([
            'api_token' => $user->remember_token
        ])->save();

        Auth::setUser($user);
    }
}
