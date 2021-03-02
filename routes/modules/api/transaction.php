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

    if (!isset($all['amount']) || !isset($all['from']) || !isset($all['to']) || !isset($all['pkey'])) {
        $code = 1001;

        $status = 4;
    } else {
        $amount = +$all['amount'];

        try {
            $url = "http://localhost:8000/send-wallet-wallet?from=".$all['from']."&to=".$all['to']."&fromKey=".$all['pkey']."&amount=".$all['amount'];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $result = curl_exec($ch);

            var_dump($result);

            curl_close ($ch);

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
    $t->status = 3;
    $t->amount = $amount;
    $t->currency = 'DLXT';
    $t->data = json_encode($result);

    $t->save();




    return json_encode($result);
});
