<?php

use App\Models\Wallet;
use App\Models\Project;

$mids = [
    \App\Http\Middleware\Authenticate::class,
    \App\Http\Middleware\CheckUser::class
];

Route::middleware($mids)->group(function () {

    Route::get('/wallet', function () {

        $q = $_GET['q'] ?? NULL;
        $searchParams = 'project, address';
        if ($q) {
            $query = Wallet::where('addr', $q);

            $project = Project::where('pref', $q)->first();

            if ($project) {
                $query->orWhere('relationId', $project->id);
            }

            $docs = $query->get();
        } else {
            $docs = Wallet::all();
        }

        $fields = Wallet::getListFields();

        return view('wallet/list', [
            'docs' => $docs,
            'fields' => $fields,
            'link' => 'wallet',
            'docsLabel' => 'Всего кошельков',
            'q' => $q,
            'searchParams' => $searchParams
        ]);
    });


    Route::get('/wallet/view/{id}', function ($id) {
        $doc = Wallet::where('id', $id)->first();
        $fields = Wallet::getViewFields();

        return view('wallet/view', [
            'doc' => $doc,
            'link' => 'wallet',
            'fields' => $fields,
            'docLabel' => 'Кошелёк',
        ]);
    });


    Route::get('/wallet/edit/{id}', function ($id) {
        $error = session()->pull('error');
        $doc = Wallet::where('id', $id)->first();

        if ($id === '0') {
            $doc = new Wallet();
            $doc->status = 1;
            $doc->type = 1;
            $doc->rootType = 0;
        }

        $fields = Wallet::defaultInputList();

        return view('wallet/edit', [
            'doc' => $doc,
            'link' => 'wallet',
            'fields' => $fields,
            'docLabel' => 'Кошелёк',
            'error' => $error
        ]);
    });

    Route::post('/wallet/edit/{id}', function ($id) {

        $modelId = $id;
        try {
            $modelId = Wallet::processPost(+$id);
        } catch (Exception $ex) {
            session(['error' => 'Ошибка: ' . $ex->getMessage()]);

            return redirect()->intended('/wallet/edit/' . $modelId);
        }

        return redirect()->intended('/wallet/view/' . $modelId);

    });

});