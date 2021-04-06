<?php

use App\Models\Log;

$mids = [
    \App\Http\Middleware\Authenticate::class,
    \App\Http\Middleware\CheckUser::class
];

Route::middleware($mids)->group(function () {

    Route::get('/log', function () {
        $q = $_GET['q'] ?? NULL;
        $page = +($_GET['page'] ?? 1);
        $limit = +($_GET['limit'] ?? 10);
        $searchParams = 'api';

        if ($q) {
            $query = Log::where('api', $q);

            $query->orderBy('date', 'desc')->get();
        } else {
            $query = Log::orderBy('date', 'desc');
        }

        $count = $query->count();

        $mxPage = ceil($count / 10);

        $docs = $query
            ->offset(($page-1) * $limit)
            ->limit($limit)
            ->get();


        $fields = Log::getListFields();

        return view('log/list', [
            'docs' => $docs,
            'fields' => $fields,
            'link' => 'log',
            'docsLabel' => 'Логи',
            'count' => $count,
            'q' => $q,
            'page' => $page,
            'searchParams' => $searchParams,
            'mxPage' => $mxPage,
        ]);
    });


});