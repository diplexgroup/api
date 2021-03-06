<?php

use App\Models\Wallet;
use App\Models\Project;
use \App\Http\Helpers\ApiHelper;
use Illuminate\Http\Request;

Route::get('/api/get-wallet-info', function (Request $request) {

    if ($errors = ApiHelper::checkAttributes([
        'wallet' => ['regex' => '/^[a-zA-Z\d]{4,}$/'],
        'key' => [],
        'project' => ['regex' => '/^[A-Z]{2,6}$/'],
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
    $toProject = Project::findByPref($all['project']);

    if (!$toProject || $toProject->status === 2) {
        return [
            'success' => false,
            'error_code' => 1012,
            'error' => 'Project Blocked'
        ];
    }

    $endpoint = $toProject->api_endpont;
    $addr = $all['wallet'] ?? '';
    $token = $toProject->token;

    $url = "$endpoint/get_wallet_info?token=".$token."&wallet=".urlencode($addr);

    try {
        $json = file_get_contents($url);

        $data = json_decode($json, true);

        $ok = $data['success'] ?? false;
        $exists = $data['exist'] ?? false;
        $locked = $data['locking'] ?? false;

        $result['success'] = $ok;
        $result['exist'] = $exists;
        $result['locking'] = $locked;


    } catch (Exception $ex) {

        $result['success'] = false;
        $result['error_code'] = 1006;

    }


    return json_encode($result);
});
