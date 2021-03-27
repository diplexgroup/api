<?php

use Illuminate\Http\Request;
use \App\Models\Transfer;
use \App\Models\Wallet;
use \App\Models\ProjectRoad;
use \App\Http\Helpers\ApiHelper;



Route::post('/api/transfer-from-comission', function (Request $request) {

    if ($errors = ApiHelper::checkAttributes([
        'amount' => ['regex' => '/^\d+(\.\d+)?$/'],
        'toUser' => ['regex' => '/^[a-zA-Z\d]{4,}$/'],
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

    // check availability
    $amount = $all['amount'];
    $toAddress = $all['toUser'];
    $error = 0;


    if (!checkWalletInfo($currentProject->api_endpont, $toAddress, $currentProject->token)) {
        $error = 1001;
    }


    try {
        $walletFrom = Wallet::getWallet($currentProject->pref, 3, NULL);

        $port = env('FLASK_PORT');
        $url = "http://localhost:".$port."/get-balance?addr=" . $walletFrom->addr;

        $data = json_decode(file_get_contents($url), true);

        if (+$data['amount'] < +$amount) {
            $error = 1105;
        }

    } catch(Exception $ex) {
        $result['success'] = false;

        $result['err'] = $ex->getMessage();

        $error = 1001;
    }


    if (!$error)  {
        $trf = Transfer::createComission($amount, $currentProject, $toAddress, $error);

        if (!$trf) {
            $error = 1106;
        } else {
            $result['transfer_id'] = $trf->trid;
        }
    }

    $result['error_code'] = $error;
    $result['success'] = !$error;


    return json_encode($result);
});
