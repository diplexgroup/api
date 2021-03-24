<?php

use App\Models\Transaction;

$mids = [
    \App\Http\Middleware\Authenticate::class,
    \App\Http\Middleware\CheckUser::class
];

Route::middleware($mids)->group(function () {

    Route::get('/transaction', function () {

        $trid = $_GET['trid'] ?? null;

        $docs = $trid ? Transaction::where('trid', $trid)->orderBy('createdAt', 'desc')->get() : Transaction::orderBy('createdAt', 'desc')->get();
        $fields = Transaction::getListFields();

        return view('transaction/list', [
            'docs' => $docs,
            'fields' => $fields,
            'link' => 'transaction',
            'docsLabel' => 'Транзакции',
        ]);
    });


});