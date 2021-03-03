<?php

use App\Models\Transaction;
use Illuminate\Http\Request;
use \App\Models\Transfer;
use \App\Models\Project;
use \App\Models\ProjectRoad;

function checkWalletInfo($endpoint, $addr, $token) {

    $url = "$endpoint/get_wallet_info?token=".$token."&wallet=".$addr;

    var_dump($url);
    try {
        $json = file_get_contents($url);

        $data = json_decode($json);

        $ok = $data['success'] ?? false;
        $exists = $data['exists'] ?? false;
        $locked = $data['locked'] ?? false;

        var_dump('data', $data);

        return $ok && $exists && !$locked;

    } catch (Exception $ex) {
        var_dump($ex->getMessage());
    }

    return false;
}


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
    $error = 0;


    if (!checkWalletInfo($currentProject->endpoint, $fromAddress, $currentProject->token) ||
     !checkWalletInfo($toProject->endpoint, $toAddress, $toProject->token)) {
        $error = 1101;
    }

    $road = null;
    if (!$error) {
        $road = ProjectRoad::getForTwoProjects($currentProject->id, $toProject->id);

        if (!$road) $error = 1102;
    }

    $trf = Transfer::create($amount, $fromProjectId, $toProjectId, $fromAddress, $toAddress, $road, $error);

    $result['error'] = $error;


    return json_encode($result);
});
