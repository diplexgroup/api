<?php

use App\Models\Project;

Route::get('/projects', function () {
    $projects = Project::all();


    return view('projects/list', ['projects' => $projects]);
});
