<?php

use App\Models\Shareholder;

$mids = [
    \App\Http\Middleware\Authenticate::class,
    \App\Http\Middleware\CheckUser::class
];

Route::middleware($mids)->group(function () {

    Route::get('/shareholder', function () {
        $q = $_GET['q'] ?? $_GET['trid'] ?? NULL;
        $searchParams = 'telegram, uid, user, sponsor_id, sponsor_user_name';

        $page = +($_GET['page'] ?? 1);
        $limit = +($_GET['limit'] ?? 10);

        if ($q) {
            $query = Shareholder::where('uid', $q);
            $query->orWhere('user', $q);
            $query->orWhere('sponsor_id', $q);
            $query->orWhere('sponsor_user_name', $q);

            $query->orderBy('telegram', 'desc');
        } else {
            $query = Shareholder::orderBy('telegram', 'desc');
        }



        $count = $query->count();

        $mxPage = ceil($count / 10);

        $docs = $query
            ->offset(($page-1) * $limit)
            ->limit($limit)
            ->get();


        $fields = Shareholder::getListFields();

        return view('shareholder/list', [
            'docs' => $docs,
            'fields' => $fields,
            'link' => 'shareholder',
            'docsLabel' => 'Акционеров',
            'count' => $count,
            'q' => $q,
            'page' => $page,
            'searchParams' => $searchParams,
            'mxPage' => $mxPage,
        ]);
    });

    Route::post('/shareholder', function () {

        $file = $_FILES['file'];

        $tmp_name = $file['tmp_name'];

        $isFirst = true;

        $rows = explode(PHP_EOL, file_get_contents($tmp_name));
        foreach ($rows as $row) {
            if ($isFirst) {
                $isFirst = false;
                continue;
            }

            $columns = explode(',', $row);

            Shareholder::createShareholder($columns[1], $columns[2], $columns[3]);
        }

        unlink($tmp_name);

        return redirect('/shareholder');
    });
});