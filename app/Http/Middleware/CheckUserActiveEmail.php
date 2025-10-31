<?php

namespace App\Http\Middleware;

use App\Constants\BaseConstants;
use App\Models\Notification;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CheckUserActiveEmail
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
        if (Auth::user()->email_verified_at == '') {
            return redirect()->route('user.activeEmail');
        }

        if (Auth::user()->active == BaseConstants::INACTIVE) {
            Auth::logout();
            return redirect()->route('index');
        }
        return $next($request);
    }
}
