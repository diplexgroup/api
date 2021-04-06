<?php

use App\Models\Transaction;

$mids = [
    \App\Http\Middleware\Authenticate::class,
    \App\Http\Middleware\CheckUser::class
];

Route::middleware($mids)->group(function () {

    Route::get('/transaction', function () {
        $q = $_GET['q'] ?? $_GET['trid'] ?? NULL;
        $searchParams = 'trid, tid';

        if ($q) {
            $query = Transaction::where('trid', $q);

            $query->orWhere('tid', $q);

            $docs = $query->orderBy('createdAt', 'desc')->get();
        } else {
            $docs = Transaction::orderBy('createdAt', 'desc')->get();
        }

        $fields = Transaction::getListFields();

        return view('transaction/list', [
            'docs' => $docs,
            'fields' => $fields,
            'link' => 'transaction',
            'docsLabel' => 'Транзакции',
            'q' => $q,
            'searchParams' => $searchParams
        ]);
    });


});