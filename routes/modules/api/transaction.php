<?php

use App\Models\Transaction;
use \App\Http\Helpers\ApiHelper;
use Illuminate\Http\Request;


Route::post('/api/transaction', function (Request $request) {

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
        'amount' => [],
        'from' => [],
        'to' => [],
        'pkey' => [],
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

    $amount = 0;
    $code = 0;
    $status = 3;

    $start = microtime(true);
    $startAt = date("Y-m-d H:i:s");


    try {
        $amount = +$all['amount'];

        $port = env('FLASK_PORT');
        $url = "http://localhost:".$port."/send-wallet-wallet?from=".$all['from']."&to=".$all['to']."&fromKey=".$all['pkey']."&amount=".$all['amount'];

        $resultData = file_get_contents($url);

        $result['resultData'] = $resultData;

        $json = json_decode($resultData, true);

        if (isset($json['result']) &&  $json['result'] !== 'success') {
            $code = 10003;
        }

    } catch (Exception $ex) {

        $result['error'] = $ex->getMessage();

        $code = 1104;
    }

    $status = $code === 0 ? 3 : 4;


    $t = new Transaction();

    $t->type = Transaction::DIRECT;
    $t->tid = Transaction::generateTid();
    $t->nextDate = '2999-01-01 00:00:00';
    $t->retryCount = '0';
    $t->errorCode = $code;
    $t->status = $status;
    $t->amount = $amount;
    $t->currency = 'DLXT';
    $t->updatedAt = date("Y-m-d H:i:s");
    $t->createdAt = $startAt;
    $t->duration =  microtime(true) - $start;
    $t->data = json_encode(array_merge($result, $all));

    $t->save();

    return json_encode($result);
});
