<?php

use App\Models\Wallet;
use App\Models\Project;
use \App\Http\Helpers\ApiHelper;
use Illuminate\Http\Request;

Route::get('/api/get_private_key', function (Request $request) {


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
        'wallet' => ['regex' => '/^[a-zA-Z\d]{10,}$/'],
    ], $request)) {
        return [
            'success' => false,
            'error_code' => 1522,
            'errors' => $errors
        ];
    }

    $w =Wallet::getByAddr($_GET['wallet']);

    if (!$w) {
        return [
            'success' => false,
            'error_code' => 1006,
        ];
    }


    return [
        'success' => true,
        'pkey' => $w->pkey,
    ];
});
