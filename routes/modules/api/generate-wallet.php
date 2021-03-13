<?php

use App\Models\Wallet;

Route::get('/api/generate-wallet', function () {
    global $currentProject;

    try {
        $model = new Wallet();

        $port = env('FLASK_PORT');
        $content = json_decode(file_get_contents('http://localhost:'.$port.'/generate-wallet'), true);

        $model->setAttr('addr', $content["base58check_address"]);
        $model->setAttr('pkey', $content["private_key"]);
        $model->setAttr('type', 1);
        $model->setAttr('rootType', 0);
        $model->setAttr('relationId', $currentProject->id);
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
    }



    return json_encode($result);
});
