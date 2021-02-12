<?php

use App\Models\Project;

Route::get('/api/projects', function () {
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
