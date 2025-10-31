<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class NeedTranslateTeamFacebook
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
        if (Auth::user()->facebook == '' || Auth::user()->phone == '') {
            return redirect()->route('user.profile');
        }
        return $next($request);
    }
}
