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

    if (!$all['amount'] || !$all['from'] || !$all['to'] || !$all['pkey']) {
        $code = 1001;

        $status = 4;
    } else {
        $amount = +$all['amount'];

        $code = 1002;

        $status = $code === 0;
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
