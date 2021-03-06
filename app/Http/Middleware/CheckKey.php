<?php

namespace App\Http\Middleware;

use App\Http\Helpers\IpHelper;
use App\Models\Project;
use Closure;

class CheckKey
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
        global $currentProject;

        $currentProject = null;

        $key = $request->get('key', NULL);
        $project = $key ? Project::where(['token' => $key, 'status' => 1])->first() : null;

        if (!$project) {
            die(json_encode(['error' => 'access denied', 'code' => 403, 'success'=>false]));
        }

        if (!IpHelper::checkProjectIp($project)) {
            die(json_encode(['error' => 'access denied: ip not in whitelist', 'code' => 403, 'success'=>false]));
        }




        $currentProject = $project;

        return $next($request);
    }
}
