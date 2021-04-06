<?php

use App\Models\Transfer;
use App\Models\Project;

$mids = [
    \App\Http\Middleware\Authenticate::class,
    \App\Http\Middleware\CheckUser::class
];

Route::middleware($mids)->group(function () {

    Route::get('/transfer', function () {

        $q = $_GET['q'] ?? $_GET['trid'] ?? NULL;
        $searchParams = 'trid, acc, project';

        if ($q) {
            $query = Transfer::where('trid', $q);

            $project = Project::where('pref', $q)->first();

            if ($project) {
                $query->orWhere('fromProject', $project->id);
                $query->orWhere('toProject', $project->id);
            }

            $query->orWhere('fromAddress', $q);
            $query->orWhere('toAddress', $q);

            $docs = $query->orderBy('dateCreated', 'desc')->get();
        } else {
            $docs = Transfer::orderBy('dateCreated', 'desc')->get();
        }

        $fields = Transfer::getListFields();

        return view('transfer/list', [
            'docs' => $docs,
            'fields' => $fields,
            'link' => 'transfer',
            'docsLabel' => 'Трансферы',
            'q' => $q,
            'searchParams' => $searchParams
        ]);
    });


});