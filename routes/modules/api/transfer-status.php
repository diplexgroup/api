<?php

use App\Models\Transfer;
use App\Models\Project;

Route::get('/api/transfer-status', function () {

    global $currentProject;

    $trId = request()->get('transferId', '');


    $transfer = Transfer::where('trid', $trId)
        ->first();

    if ($transfer->fromProject !== $currentProject->id && $transfer->toProject !== $currentProject || !$transfer) {
        $result = [
            'success' => false
        ];

    } else {

        $result = [
            'success' => true,
            'fromProject' => Project::getShort($transfer->fromProject),
            'toProject' => Project::getShort($transfer->toProject),
            'fromUser' => $transfer->fromAddress,
            'toUser' => $transfer->toAddress,
            'step' => $transfer->step,
            'amount' => $transfer->amount,
            'status' => $transfer->status,
        ];
    }


    return json_encode($result);
});
