<?php

use App\Models\Transaction;
use Illuminate\Http\Request;


Route::post('/api/transfer', function (Request $request) {

    $result = [
      'success' => true
    ];

    $all = $request->all();

    echo 'PHP version: ' . phpversion();

die();
    // create curl resource
    $ch = curl_init();

    // set url
    curl_setopt($ch, CURLOPT_URL, "https://example.com");

    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // $output contains the output string
    $output = curl_exec($ch);

    // close curl resource to free up system resources
    curl_close($ch);

    echo $output;


//    $amount = 0;
//    $code = 0;
//    $status = 3;

//    if (!isset($all['amount']) || !isset($all['from']) || !isset($all['to']) || !isset($all['pkey'])) {
//        $code = 1001;
//
//        $status = 4;
//    }

//    $t = new Transfer();
//
//
//
//    $t->save();

    return json_encode($result);
});
