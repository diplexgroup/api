<?php


$mids = [
    \App\Http\Middleware\Authenticate::class,
    \App\Http\Middleware\CheckUser::class
];

Route::middleware($mids)->group(function () {

    Route::get('/send-wallet-wallet', function () {
        $error = session()->pull('error');
        $explorer_id = session()->pull('explorer_id');

        return view('send-wallet-wallet', [
            'link' => 'send-wallet-wallet',
            'error' => $error,
            'explorer_id' => $explorer_id,
        ]);
    });

    Route::post('/send-wallet-wallet', function () {
        $sender_wallet = $_POST['sender-wallet'];
        $sender_priv_key = $_POST['sender-priv-key'];
        $receiver_wallet = $_POST['receiver-wallet'];
        $amount = (float) $_POST['amount'];

        $port = env('FLASK_PORT');
        $url = "http://localhost:".$port."/send-wallet-wallet?from=".$sender_wallet."&to=".$receiver_wallet."&fromKey=".$sender_priv_key."&amount=".$amount;
//        var_dump($url);
        $error = '';
        $explorer_id = '';
        try {

            $resultData = file_get_contents($url);

            $result['resultData'] = $resultData;

            $json = json_decode($resultData, true);

            if (isset($json['receipt']) && isset($json['receipt']['result']) && $json['receipt']['result'] == 'SUCCESS') {

                $explorer_id = $json['id'];
            } else {
                $error = $resultData;
            }

        } catch (\Exception $ex) {
            $error = $ex->getMessage();
        }
        return view('send-wallet-wallet', [
            'link' => 'send-wallet-wallet',
            'error' => $error,
            'explorer_id' => $explorer_id,
        ]);

    });


});
