<?php

use App\Models\Currency;

$mids = [
    \App\Http\Middleware\Authenticate::class,
    \App\Http\Middleware\CheckUser::class
];

Route::middleware($mids)->group(function () {

    Route::get('/currency', function () {
        $q = $_GET['q'] ?? NULL;
        $searchParams = 'code';
        if ($q) {
            $query = Currency::where('code', $q);

            $docs = $query->get();
        } else {
            $docs = Currency::all();
        }
        $fields = Currency::getListFields();

        return view('currency/list', [
            'docs' => $docs,
            'fields' => $fields,
            'link' => 'currency',
            'docsLabel' => 'Всего валют',
            'q' => $q,
            'searchParams' => $searchParams
        ]);
    });


    Route::get('/currency/view/{id}', function ($id) {
        $doc = Currency::where('id', $id)->first();
        $fields = Currency::getViewFields();

        return view('currency/view', [
            'doc' => $doc,
            'link' => 'currency',
            'fields' => $fields,
            'docLabel' => 'Валюта',
        ]);
    });


    Route::get('/currency/edit/{id}', function ($id) {
        $error = session()->pull('error');
        $doc = Currency::where('id', $id)->first();

        if ($id === '0') {
            $doc = new Currency();
            $dec->type = 1;
            $dec->toDLX = 0;
        }

        $fields = Currency::defaultInputList();

        return view('currency/edit', [
            'doc' => $doc,
            'link' => 'currency',
            'fields' => $fields,
            'docLabel' => 'Валюта',
            'error' => $error
        ]);
    });

    Route::post('/currency/edit/{id}', function ($id) {

        $modelId = $id;
        try {
            $modelId = Currency::processPost(+$id);
        } catch (Exception $ex) {
            session(['error' => 'Ошибка: ' . $ex->getMessage()]);

            return redirect()->intended('/currency/edit/' . $modelId);
        }

        return redirect()->intended('/currency/view/' . $modelId);

    });

});