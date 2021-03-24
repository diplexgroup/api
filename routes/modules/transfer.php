<?php

use App\Models\Transfer;

$mids = [
    \App\Http\Middleware\Authenticate::class,
    \App\Http\Middleware\CheckUser::class
];

Route::middleware($mids)->group(function () {

    Route::get('/transfer', function () {
        $docs = Transfer::orderBy('dateCreated', 'desc')->get();
        $fields = Transfer::getListFields();

        return view('transfer/list', [
            'docs' => $docs,
            'fields' => $fields,
            'link' => 'transfer',
            'docsLabel' => 'Трансферы',
        ]);
    });


});