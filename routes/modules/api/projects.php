<?php

use App\Models\Project;
use \App\Http\Helpers\ApiHelper;
use Illuminate\Http\Request;

Route::get('/api/projects', function (Request $request) {

    global $currentProject;

    if ($currentProject->status === 2) {
        return [
            'success' => false,
            'error_code' => 1011,
            'error' => 'Project Blocked'
        ];
    }

    if ($errors = ApiHelper::checkAttributes([
        'key' => [],
    ], $request)) {
        return [
            'success' => false,
            'error_code' => 1522,
            'errors' => $errors
        ];
    }

    $projects = Project::where(['status' => 1])->get()->all();

    $result = [
      'success' => true
    ];

    $result['items'] = array_map(function($item) {
        return [
            'name' => $item->name,
            'short' => $item->pref,
            'description' => $item->description,
            'link' => $item->api_front_link
        ];
    }, $projects);


    return json_encode($result);
});
