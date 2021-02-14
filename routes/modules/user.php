<?php

use App\User;

$mids = [
    \App\Http\Middleware\Authenticate::class,
    \App\Http\Middleware\CheckUser::class
];

Route::middleware($mids)->group(function () {

    Route::get('/users', function () {
        $docs = User::all();
        $fields = User::getListFields();

        return view('users/list', [
            'docs' => $docs,
            'fields' => $fields,
            'link' => 'users',
            'docsLabel' => 'Всего пользователей',
        ]);
    });


    Route::get('/users/view/{id}', function ($id) {
        $doc = User::where('id', $id)->first();
        $fields = User::getViewFields();

        return view('users/view', [
            'doc' => $doc,
            'link' => 'users',
            'fields' => $fields,
            'docLabel' => 'Пользователь',
        ]);
    });


    Route::get('/users/edit/{id}', function ($id) {
        $error = session()->pull('error');
        $doc = User::where('id', $id)->first();

        if ($id === '0') {
            $doc = new User();
            $doc->status = 1;
        }

        $fields = User::defaultInputList();

        return view('users/edit', [
            'doc' => $doc,
            'link' => 'users',
            'fields' => $fields,
            'docLabel' => 'Пользователь',
            'error' => $error
        ]);
    });

    Route::post('/users/edit/{id}', function ($id) {

        $modelId = $id;
        try {
            $modelId = User::processPost(+$id);
        } catch (Exception $ex) {
            session(['error' => 'Ошибка: ' . $ex->getMessage()]);

            return redirect()->intended('/users/edit/' . $modelId);
        }

        return redirect()->intended('/users/view/' . $modelId);

    });

});