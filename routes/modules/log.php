<?php

use App\Models\Log;

$mids = [
    \App\Http\Middleware\Authenticate::class,
    \App\Http\Middleware\CheckUser::class
];

Route::middleware($mids)->group(function () {

    Route::get('/log', function () {
        $docs = Log::all();
        $fields = Log::getListFields();

        return view('log/list', [
            'docs' => $docs,
            'fields' => $fields,
            'link' => 'log',
            'docsLabel' => 'Логи',
        ]);
    });


});