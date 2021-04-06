<?php

use App\Models\Transfer;
use App\Models\Project;

$mids = [
    \App\Http\Middleware\Authenticate::class,
    \App\Http\Middleware\CheckUser::class
];

Route::middleware($mids)->group(function () {

    Route::get('/transfer', function () {


        $page = +($_GET['page'] ?? 1);
        $limit = +($_GET['limit'] ?? 1);


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

            $query->orderBy('dateCreated', 'desc');
        } else {
            $query = Transfer::orderBy('dateCreated', 'desc');


        }

        $count = $query->count();

        $mxPage = ceil($count / 10);

        $docs = $query
            ->offset(($page-1) * $limit)
            ->limit($limit)
            ->get();

        $fields = Transfer::getListFields();

        return view('transfer/list', [
            'docs' => $docs,
            'fields' => $fields,
            'link' => 'transfer',
            'docsLabel' => 'Трансферы',
            'count' => $count,
            'q' => $q,
            'searchParams' => $searchParams,
            'mxPage' => $mxPage,
        ]);
    });


});