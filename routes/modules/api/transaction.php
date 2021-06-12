<?php

use App\Models\Transaction;
use App\Models\Wallet;
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
        'amount' => ['regex' => '/^\d+(\.\d+)?$/'],
        'from' => ['regex' => '/^[a-zA-Z\d]{4,}$/'],
        'to' => ['regex' => '/^[a-zA-Z\d]{4,}$/'],
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

    $start = microtime(true);
    $startAt = date("Y-m-d H:i:s");


    try {
        $from = $all['from'];

        $wallet = $from === 'main' ? Wallet::getWallet($currentProject->id,1, NULL) : Wallet::where('addr', $all['from'])->first();

        $amount = +$all['amount'];

        $port = env('FLASK_PORT');

        $to = $all['to'];

        if ($to === 'main') {
            $w = Wallet::getWallet($currentProject->id,1, NULL);
            $to = $w->addr;
        }

        $url = "http://localhost:".$port."/send-wallet-wallet?from=".($wallet->addr)."&to=".$to."&fromKey=".($wallet -> pkey)."&amount=".$all['amount'];

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
    $result['success'] = $code === 0;


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
