<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Storage;


class LogRequest {

    public function handle($request, Closure $next)
    {
        $result = $next($request);

        $log = new \App\Models\Log();

        global $currentProject;

        $log->projectId = $currentProject ? $currentProject->id : null;

        $url = preg_replace('/\?.+/', '', $request->getUri());

        $parts = explode('/', $url);

        $log->api = $parts[sizeof($parts) - 1];

        $log->request = json_encode($request->all());

        $logFile = uniqid(date('Y-m-d_His')) . '.txt';

        try {
            $context = json_encode($result);
        } catch (\Exception $ex) {
            if (is_string($result)) {
                $context = $result;
            } else {
                $context = 'no data';
            }
        }

        if (!$context || !strlen($context)) {
            $context = 'no data';
        }

        Storage::disk('api_logs')->put($logFile, $context);

        $log->response = $logFile;

        $log->date = date("Y-m-d H:i:s");

        $log->save();

        return $result;
    }


}