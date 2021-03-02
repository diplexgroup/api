<?php

use App\Models\ProjectRoad;
use App\Models\Project;
use Illuminate\Http\Request;

Route::post('/api/transaction', function (Request $request) {

    $result = [
      'success' => true
    ];

    var_dump($request->all());

    $map = [];

    global $currentProject;
    $map[$currentProject->id] = $currentProject;


    return json_encode($result);
});
