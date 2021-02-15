<?php

namespace App\Http\Middleware;

use App\Http\Helpers\IpHelper;
use App\Models\Modules;
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

        $parts = explode('/', $request->getUri());
        $link = $parts[3] ?? '';

        $module = Modules::where('link', $link)->first();

        if ($module && $user) {
            $roles = explode(',', $user->roles ?? '');
            $field = $request->getMethod() === 'GET' ? 'readRoles' : 'writeRoles';
            $moduleRoles = explode(',', $module->$field ?? '');

            $interSect = array_intersect($roles, $moduleRoles);

            if (!sizeof($interSect)) {
                die('Fobbiden');
            }
        }


        return $next($request);
    }
}