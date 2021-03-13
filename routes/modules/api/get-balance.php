<?php

use App\Models\Wallet;

Route::get('/api/get-balance', function () {

    $addr = request()->get('address', '');

    try {

        $port = env('FLASK_PORT');
        $url = "http://localhost:".$port."/get-balance?addr=" + $addr;
var_dump($url);
var_dump(file_get_contents($url));
        $data = json_decode(file_get_contents($url), true);

        $result = [
            'success' => true,
            'amount' => $data['amount'],
        ];

    } catch(Exception $ex) {
        $result['success'] = false;
    }


    return json_encode($result);
});
