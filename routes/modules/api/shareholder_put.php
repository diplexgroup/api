<?php

use App\Models\Shareholder;
use \App\Http\Helpers\ApiHelper;
use Illuminate\Http\Request;

Route::post('/api/shareholder-put', function (Request $request) {

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
        'telegram' => [],
        'telegramId' => [],
        'sponsor' => [],
        'sponsorId' => [],
    ], $request)) {
        return [
            'success' => false,
            'error_code' => 1522,
            'errors' => $errors
        ];
    }

    $userId = $request->get('telegramId', NULL);
    $sh = $userId ? Shareholder::where('uid', $userId)->first() : NULL;

    Shareholder::createShareholder(0, $request->get('telegram'), $request->get('telegramId'), $request->get('sponsor'), $request->get('sponsorId'), $sh ? ($sh->type | 2) : 2);

    $result = [
        'success' => true,
    ];


    return json_encode($result);
});
