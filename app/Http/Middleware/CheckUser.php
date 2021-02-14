<?php

namespace App\Http\Middleware;

use App\Http\Helpers\IpHelper;
use App\Models\Project;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class CheckUser
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

        $user = Auth::user();

        if (!$user || $user->status !== 1 || !IpHelper::checkIp()) {
           Auth::logout();

           return redirect('/');
        }


        return $next($request);
    }
}