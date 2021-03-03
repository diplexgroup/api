<?php

use App\Models\Transaction;
use Illuminate\Http\Request;
use \App\Models\Transfer;
use \App\Models\Project;
use \App\Models\ProjectRoad;

Route::post('/api/transfer', function (Request $request) {

    $result = [
      'success' => true
    ];

    $all = $request->all();

    global $currentProject;

    //checks
    $toProject = Project::findByPref($all['toProj']);

    // check availability
    $amount = $all['amount'];
    $fromProjectId = $currentProject->id;
    $toProjectId = $toProject->id;
    $fromAddress = $all['fromUser'];
    $toAddress = $all['toUser'];


    $trf = Transfer::create($amount, $fromProjectId, $toProjectId, $fromAddress, $toAddress);



    return json_encode($result);
});
