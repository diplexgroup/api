<?php

use App\Models\ProjectRoad;
use App\Models\Project;

Route::post('/api/transaction', function () {

    $result = [
      'success' => true
    ];

    $map = [];

    global $currentProject;
    $map[$currentProject->id] = $currentProject;

    var_dump($_POST);

    return json_encode($result);
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
