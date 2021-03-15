<?php

use App\Models\Wallet;
use App\Models\Project;
use \App\Http\Helpers\ApiHelper;
use Illuminate\Http\Request;

Route::get('/api/generate-wallet', function (Request $request) {


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

    $project = Project::where('pref', 'OUT')->first();

    try {
        $model = new Wallet();

        $port = env('FLASK_PORT');
        $content = json_decode(file_get_contents('http://localhost:'.$port.'/generate-wallet'), true);

        $model->setAttr('addr', $content["base58check_address"]);
        $model->setAttr('pkey', $content["private_key"]);
        $model->setAttr('type', 1);
        $model->setAttr('rootType', 0);
        $model->setAttr('relationId', $project->id);
        $model->setAttr('currency', 'DLXT');
        $model->setAttr('status', 1);

        $result = [
            'success' => true,
            'wallet' => [
                'addr' => $model->addr,
                'pkey' => $model->pkey,
                'currency' => $model->currency
            ]
        ];

        $model->save();

    } catch(Exception $ex) {
        $result['success'] = false;

        $result['error_code'] = 1102;
    }



    return json_encode($result);
});
