<?php

use App\Models\Wallet;
use \App\Http\Helpers\ApiHelper;
use Illuminate\Http\Request;

Route::get('/api/get-balance', function (Request $request) {

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
        'address' => ['regex' => '/^[a-zA-Z\d]{10,}$/'],
    ], $request)) {
        return [
            'success' => false,
            'error_code' => 1522,
            'errors' => $errors
        ];
    }

    $addr = request()->get('address', '');

    try {

        $port = env('FLASK_PORT');
        $url = "http://localhost:".$port."/get-balance?addr=" . $addr;

        $data = json_decode(file_get_contents($url), true);

        $result = [
            'success' => true,
            'amount' => $data['amount'],
        ];

    } catch(Exception $ex) {
        $result['success'] = false;

        $result['error_code'] = 1001;
    }


    return json_encode($result);
});
