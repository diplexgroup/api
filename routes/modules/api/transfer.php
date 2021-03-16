<?php

use App\Models\Transaction;
use Illuminate\Http\Request;
use \App\Models\Transfer;
use \App\Models\Project;
use \App\Models\ProjectRoad;
use \App\Http\Helpers\ApiHelper;

function checkWalletInfo($endpoint, $addr, $token) {

    $url = "$endpoint/get_wallet_info?token=".$token."&wallet=".$addr;


    try {
        $json = file_get_contents($url);

        $data = json_decode($json, true);

        $ok = $data['success'] ?? false;
        $exists = $data['exist'] ?? false;
        $locked = $data['locking'] ?? false;

        return $ok && $exists && !$locked;

    } catch (Exception $ex) {
    }

    return false;
}


Route::post('/api/transfer', function (Request $request) {

    if ($errors = ApiHelper::checkAttributes([
        'amount' => [],
        'fromUser' => [],
        'toUser' => [],
        'key' => [],
    ], $request)) {
        return [
            'success' => false,
            'error_code' => 1522,
            'errors' => $errors
        ];
    }

    $result = [
      'success' => true
    ];

    $all = $request->all();

    global $currentProject;

    if ($currentProject->status === 2) {
        return [
            'success' => false,
            'error_code' => 1011,
            'error' => 'Project Blocked'
        ];
    }

    //checks
    $toProject = Project::findByPref($all['toProj']);

    // check availability
    $amount = $all['amount'];
    $fromAddress = $all['fromUser'];
    $toAddress = $all['toUser'];
    $error = 0;

    if (!$toProject || $toProject->status === 2) {
        return [
            'success' => false,
            'error_code' => 1012,
            'error' => 'Project Blocked'
        ];
    }


    if (!checkWalletInfo($currentProject->api_endpont, $fromAddress, $currentProject->token)) {
        $error = 1001;
    }

    if (!checkWalletInfo($toProject->api_endpont, $toAddress, $toProject->token)) {
        $error = 1003;
    }


    $road = ProjectRoad::getForTwoProjects($currentProject->id, $toProject->id);

    if (!$road) $error = 1005;

    if (!$error) {
        $trf = Transfer::create($amount, $currentProject, $toProject, $fromAddress, $toAddress, $road, $error);

        if (!$trf) {
            return 1106;
        }
    }

    $result['error_code'] = $error;
    $result['success'] = !$error;


    return json_encode($result);
});
