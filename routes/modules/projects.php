<?php

use App\Models\Project;

$mids = [
    \App\Http\Middleware\Authenticate::class,
    \App\Http\Middleware\CheckUser::class
];

Route::middleware($mids)->group(function () {

    Route::get('/projects', function () {
        $q = $_GET['q'] ?? NULL;
        $searchParams = 'pref, endpont, link, name';
        if ($q) {
            $query = Project::where('pref', $q);

            $query->orWhere('name', 'like', '%' . $q . '%');

            if (Strlen($q) > 6) {
                $query->orWhere('api_endpont', 'like',  '%' . $q . '%');
                $query->orWhere('api_front_link', 'like', '%' . $q . '%');
            }


            $docs = $query->get();
        } else {
            $docs = Project::all();
        }

        $fields = Project::getListFields();

        return view('projects/list', [
            'docs' => $docs,
            'fields' => $fields,
            'link' => 'projects',
            'docsLabel' => 'Всего проектов',
            'q' => $q,
            'searchParams' => $searchParams
        ]);
    });


    Route::get('/projects/view/{id}', function ($id) {
        $doc = Project::where('id', $id)->first();
        $fields = Project::getViewFields();

        return view('projects/view', [
            'doc' => $doc,
            'link' => 'projects',
            'fields' => $fields,
            'docLabel' => 'Проект',
        ]);
    });


    Route::get('/projects/edit/{id}', function ($id) {
        $error = session()->pull('error');
        $doc = Project::where('id', $id)->first();

        if ($id === '0') {
            $doc = new Project();
            $doc->type = 1;
            $doc->addr_need_flag = 1;
            $doc->status = 1;
        }

        $fields = Project::defaultInputList();

        return view('projects/edit', [
            'doc' => $doc,
            'link' => 'projects',
            'fields' => $fields,
            'docLabel' => 'Проект',
            'error' => $error
        ]);
    });

    Route::post('/projects/edit/{id}', function ($id) {

        $modelId = $id;
        try {
            $modelId = Project::processPost(+$id);
        } catch (Exception $ex) {
            session(['error' => 'Ошибка: ' . $ex->getMessage()]);

            return redirect()->intended('/projects/edit/' . $modelId);
        }

        return redirect()->intended('/projects/view/' . $modelId);

    });

});