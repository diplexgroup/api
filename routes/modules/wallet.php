<?php

use App\Models\Wallet;

Route::middleware([\App\Http\Middleware\Authenticate::class])->group(function () {

    Route::get('/wallet', function () {
        $docs = Wallet::all();
        $fields = Wallet::getListFields();

        return view('wallet/list', [
            'docs' => $docs,
            'fields' => $fields,
            'link' => 'wallet',
            'docsLabel' => 'Всего кошельков',
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