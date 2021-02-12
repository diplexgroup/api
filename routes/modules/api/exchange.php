<?php

use App\Models\Currency;

Route::get('/api/exchange', function () {
    $projects = Currency::where([])->get()->all();

    $result = [
      'success' => true
    ];

    $result['items'] = array_map(function($item) {
        return [
            'name' => $item->name,
            'code' => $item->code,
            'type' => $item->type,
            'toDLX' => number_format($item->inDLX, 12, '.', '')
        ];
    }, $projects);


    return json_encode($result);
});
