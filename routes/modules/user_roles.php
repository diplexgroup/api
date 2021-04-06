<?php

use App\Models\UserRole;

$mids = [
    \App\Http\Middleware\Authenticate::class,
    \App\Http\Middleware\CheckUser::class
];

Route::middleware($mids)->group(function () {

    Route::get('/user_roles', function () {

        $q = $_GET['q'] ?? NULL;
        $searchParams = 'name';
        if ($q) {
            $query = UserRole::where('name', 'like', '%' . $q . '%');

            $docs = $query->get();
        } else {
            $docs = UserRole::all();
        }

        $fields = UserRole::getListFields();

        return view('user_roles/list', [
            'docs' => $docs,
            'fields' => $fields,
            'link' => 'user_roles',
            'docsLabel' => 'Всего ролей',
            'q' => $q,
            'searchParams' => $searchParams
        ]);
    });


    Route::get('/user_roles/view/{id}', function ($id) {
        $doc = UserRole::where('id', $id)->first();
        $fields = UserRole::getViewFields();

        return view('user_roles/view', [
            'doc' => $doc,
            'link' => 'user_roles',
            'fields' => $fields,
            'docLabel' => 'Роль',
        ]);
    });


    Route::get('/user_roles/edit/{id}', function ($id) {
        $error = session()->pull('error');
        $doc = UserRole::where('id', $id)->first();

        if ($id === '0') {
            $doc = new UserRole();
        }

        $fields = UserRole::defaultInputList();

        return view('user_roles/edit', [
            'doc' => $doc,
            'link' => 'user_roles',
            'fields' => $fields,
            'docLabel' => 'Роль',
            'error' => $error
        ]);
    });

    Route::post('/user_roles/edit/{id}', function ($id) {

        $modelId = $id;
        try {
            $modelId = UserRole::processPost(+$id);
        } catch (Exception $ex) {
            session(['error' => 'Ошибка: ' . $ex->getMessage()]);

            return redirect()->intended('/user_roles/edit/' . $modelId);
        }

        return redirect()->intended('/user_roles/view/' . $modelId);

    });

});