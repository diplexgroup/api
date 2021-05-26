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
    ], $request)) {
        return [
            'success' => false,
            'error_code' => 1522,
            'errors' => $errors
        ];
    }

    $addrs = ['telegram', 'telegramId', 'sponsor', 'sponsorId', 'type'];

    foreach ($addrs as $addr) {
        $val = $_POST[$addr] ?? NULL;
        if (!$val) {
            return [
                'success' => false,
                'error_code' => 1522,
                'errors' => [
                    'error' => $addr . ' should be set'
                ]
            ];
        }
    }

    $userId = $_POST['telegramId'] ?? NULL;
    $sh = $userId ? Shareholder::where('uid', $userId)->first() : NULL;

    Shareholder::createShareholder(0, $_POST['telegram'], $_POST['telegramId'], $_POST['sponsor'], $_POST['sponsorId'], $sh ? ($sh->type | 2) : 2);

    $result = [
        'success' => true,
    ];


    return json_encode($result);
});
