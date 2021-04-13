<?php

use App\Models\Shareholder;
use \App\Http\Helpers\ApiHelper;
use Illuminate\Http\Request;

Route::get('/api/shareholder', function (Request $request) {

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

    $tid = $_GET['telegram'] ?? NULL;
    $user_id = $_GET['uid'] ?? NULL;

    if (!$tid && !$user_id) {
        return [
            'success' => false,
            'error_code' => 1522,
            'errors' => [
                'error' => 'uid or telegram_id should be set'
            ]
        ];
    }

    $sh = ($tid ? Shareholder::where('telegram', 'like', '%'.$tid.'%') : Shareholder::where('uid', 'like', $user_id))->first();

    if (!$sh) {
        return [
            'success' => false,
            'error_code' => 1001,
            'error' => 'no such user'
        ]
    }

    $result = [
        'success' => true,
        'telgram_id' => $sh
    ];


    return json_encode($result);
});
