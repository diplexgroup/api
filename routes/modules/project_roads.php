<?php

use App\Models\ProjectRoad;

$mids = [
    \App\Http\Middleware\Authenticate::class,
    \App\Http\Middleware\CheckUser::class
];

Route::middleware($mids)->group(function () {

    Route::get('/project_roads', function () {
        $docs = ProjectRoad::all();
        $fields = ProjectRoad::getListFields();

        return view('project_roads/list', [
            'docs' => $docs,
            'fields' => $fields,
            'link' => 'project_roads',
            'docsLabel' => 'Всего связей',
        ]);
    });


    Route::get('/project_roads/view/{id}', function ($id) {
        $doc = ProjectRoad::where('id', $id)->first();
        $fields = ProjectRoad::getViewFields();

        return view('project_roads/view', [
            'doc' => $doc,
            'link' => 'project_roads',
            'fields' => $fields,
            'docLabel' => 'Связь',
        ]);
    });


    Route::get('/project_roads/edit/{id}', function ($id) {
        $error = session()->pull('error');
        $doc = ProjectRoad::where('id', $id)->first();

        if ($id === '0') {
            $doc = new ProjectRoad();
            $doc->status = 1;
            $doc->minAmount = 0;
            $doc->maxAmount = 1000000;
            $doc->burnPercent = 0;
        }

        $fields = ProjectRoad::defaultInputList();

        return view('project_roads/edit', [
            'doc' => $doc,
            'link' => 'project_roads',
            'fields' => $fields,
            'docLabel' => 'Связь',
            'error' => $error
        ]);
    });

    Route::post('/project_roads/edit/{id}', function ($id) {

        $modelId = $id;
        try {
            $modelId = ProjectRoad::processPost(+$id);
        } catch (Exception $ex) {
            session(['error' => 'Ошибка: ' . $ex->getMessage()]);

            return redirect()->intended('/project_roads/edit/' . $modelId);
        }

        return redirect()->intended('/project_roads/view/' . $modelId);

    });

});