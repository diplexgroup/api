<?php

namespace App\Http\Middleware;

use Closure;


class LogRequest {

    public function handle($request, Closure $next)
    {
        $result = $next($request);

        $response = $next($request);

        $log = new \App\Models\Log();

        global $currentProject;

        $log->projectId = $currentProject ? $currentProject->id : null;

        $url = preg_replace('/\?.+/', '', $request->getUri());

        $parts = explode('/', $url);

        $log->api = $parts[sizeof($parts) - 1];

        $log->request = json_encode($request->all());

        $log->response = json_encode($request->all());

        $log->date = date("Y-m-d H:i:s");

        $log->save();

        return $result;
    }


}