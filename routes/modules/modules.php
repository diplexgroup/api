<?php

use App\Models\Modules;

$mids = [
    \App\Http\Middleware\Authenticate::class,
    \App\Http\Middleware\CheckUser::class
];

Route::middleware($mids)->group(function () {

    Route::get('/modules', function () {
        $q = $_GET['q'] ?? NULL;
        $searchParams = 'name';
        if ($q) {
            $query = Modules::where('name', $q);

            $docs = $query->get();
        } else {
            $docs = Modules::all();
        }

        $fields = Modules::getListFields();

        return view('modules/list', [
            'docs' => $docs,
            'fields' => $fields,
            'link' => 'modules',
            'docsLabel' => 'Всего модулей',
            'q' => $q,
            'searchParams' => $searchParams
        ]);
    });


    Route::get('/modules/view/{id}', function ($id) {
        $doc = Modules::where('id', $id)->first();
        $fields = Modules::getViewFields();

        return view('modules/view', [
            'doc' => $doc,
            'link' => 'modules',
            'fields' => $fields,
            'docLabel' => 'Модуль',
        ]);
    });


    Route::get('/modules/edit/{id}', function ($id) {
        $error = session()->pull('error');
        $doc = Modules::where('id', $id)->first();

        if ($id === '0') {
            $doc = new Modules();
        }

        $fields = Modules::defaultInputList();

        return view('modules/edit', [
            'doc' => $doc,
            'link' => 'modules',
            'fields' => $fields,
            'docLabel' => 'Модуль',
            'error' => $error
        ]);
    });

    Route::post('/modules/edit/{id}', function ($id) {

        $modelId = $id;
        try {
            $modelId = Modules::processPost(+$id);
        } catch (Exception $ex) {
            session(['error' => 'Ошибка: ' . $ex->getMessage()]);

            return redirect()->intended('/modules/edit/' . $modelId);
        }

        return redirect()->intended('/modules/view/' . $modelId);

    });

});