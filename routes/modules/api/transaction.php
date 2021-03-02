<?php

use App\Models\Transaction;
use Illuminate\Http\Request;

Route::post('/api/transaction', function (Request $request) {

    $result = [
      'success' => true
    ];

    $all = $request->all();

    $amount = 0;
    $code = 0;
    $status = 3;

    if (!isset($all['amount']) || !isset($all['from']) || !isset($all['to']) || !isset($all['pkey'])) {
        $code = 1001;

        $status = 4;
    } else {
        $amount = +$all['amount'];

        try {
            $url = "http://localhost:8000/send-wallet-wallet?from=".$all['from']."&to=".$all['to']."&fromKey=".$all['pkey']."&amount=".$all['amount'];


            $result = file_get_contents($url);

            var_dump($result);

        } catch (Exception $ex) {
            var_dump($ex->getMessage());

            $code = 1002;
        }

        $status = $code === 0 ? 3 : 4;
    }

    $result = array_merge($result, $all);

    $t = new Transaction();

    $t->type = Transaction::DIRECT;
    $t->tid = Transaction::generateTid();
    $t->nextDate = '2999-01-01 00:00:00';
    $t->retryCount = '0';
    $t->errorCode = $code;
    $t->status = $status;
    $t->amount = $amount;
    $t->currency = 'DLXT';
    $t->data = json_encode($result);

    $t->save();




    return json_encode($result);
});
