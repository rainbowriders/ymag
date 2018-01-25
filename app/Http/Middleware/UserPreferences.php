<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Config;

class UserPreferences
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check()) {
            $me = auth()->user();

            $this->setLanguage($me);
            $this->setTheme($me);
        }

        return $next($request);
    }

    protected function setLanguage($me)
    {
        if (($lang = $me->lang()) !== config('app.locale')) {
            Config::set('app.locale', $lang);
            Carbon::setLocale($lang);
        }
    }

    /**
     * @param $me
     */
    protected function setTheme($me)
    {
        if (($theme = $me->theme()) !== config('app.theme')) {
            if (! in_array($theme, $themes = config('app.themes'))) {
                $theme = array_first($themes);
            }
            Config::set('app.theme', $theme);
        }
    }
}
