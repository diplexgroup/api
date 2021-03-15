<?php

use App\Models\Wallet;
use \App\Http\Helpers\ApiHelper;
use Illuminate\Http\Request;

Route::get('/api/get-balance', function (Request $request) {

    if ($errors = ApiHelper::checkAttributes([
        'key' => [],
        'address' => [],
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
        var_dump($ex);
        $result['success'] = false;
    }


    return json_encode($result);
});
