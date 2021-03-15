<?php

use App\Models\Currency;
use \App\Http\Helpers\ApiHelper;
use Illuminate\Http\Request;

Route::get('/api/exchange', function (Request $request) {

    global $currentProject;

    if ($currentProject->status === 2) {
        return [
            'success' => false,
            'error_code' => 1011,
            'error' => 'Project Blocked'
        ];
    }

    if ($errors = ApiHelper::checkAttributes([
        'key' => [],
    ], $request)) {
        return [
            'success' => false,
            'error_code' => 1522,
            'errors' => $errors
        ];
    }

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
