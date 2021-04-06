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

        $page = +($_GET['page'] ?? 1);
        $limit = +($_GET['limit'] ?? 10);

        if ($q) {
            $query = Transaction::where('trid', $q);

            $query->orWhere('tid', $q);

            $query->orderBy('createdAt', 'desc');
        } else {
            $query = Transaction::orderBy('createdAt', 'desc');
        }



        $count = $query->count();

        $mxPage = ceil($count / 10);

        $docs = $query
            ->offset(($page-1) * $limit)
            ->limit($limit)
            ->get();


        $fields = Transaction::getListFields();

        return view('transaction/list', [
            'docs' => $docs,
            'fields' => $fields,
            'link' => 'transaction',
            'docsLabel' => 'Транзакции',
            'count' => $count,
            'q' => $q,
            'page' => $page,
            'searchParams' => $searchParams,
            'mxPage' => $mxPage,
        ]);
    });


});