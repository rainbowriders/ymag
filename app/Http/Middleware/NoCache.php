<?php

namespace App\Http\Middleware;

use Closure;

class NoCache
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $next = $next($request);

        $next->withHeaders([
            'Last-Modified' => gmdate("D, d M Y H:i:s") . ' GMT',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
            'Pragma' => 'no-cache',
            'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT',
        ]);

        return $next;
    }
}
