<?php

use App\Models\Log;

$mids = [
    \App\Http\Middleware\Authenticate::class,
    \App\Http\Middleware\CheckUser::class
];

Route::middleware($mids)->group(function () {

    Route::get('/log', function () {
        $q = $_GET['q'] ?? NULL;
        $searchParams = 'api';

        if ($q) {
            $query = Log::where('api', $q);

            $docs = $query->orderBy('date', 'desc')->get();
        } else {
            $docs = Log::orderBy('date', 'desc')->get();
        }
        $fields = Log::getListFields();

        return view('log/list', [
            'docs' => $docs,
            'fields' => $fields,
            'link' => 'log',
            'docsLabel' => 'Логи',
            'q' => $q,
            'searchParams' => $searchParams
        ]);
    });


});