<?php

use App\Models\Transaction;

$mids = [
    \App\Http\Middleware\Authenticate::class,
    \App\Http\Middleware\CheckUser::class
];

Route::middleware($mids)->group(function () {

    Route::get('/transaction', function () {
        $docs = Transaction::all();
        $fields = Transaction::getListFields();

        return view('transaction/list', [
            'docs' => $docs,
            'fields' => $fields,
            'link' => 'transaction',
            'docsLabel' => 'Транзакции',
        ]);
    });


});