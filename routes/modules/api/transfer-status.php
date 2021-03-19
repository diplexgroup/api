<?php

use App\Models\Transfer;
use App\Models\Transaction;
use App\Models\Project;
use \App\Http\Helpers\ApiHelper;
use Illuminate\Http\Request;

Route::get('/api/transfer-status', function (Request $request) {

    global $currentProject;

    if ($currentProject->status === 2) {
        return [
            'success' => false,
            'error_code' => 1011,
            'error' => 'Project Blocked'
        ];
    }

    $trId = request()->get('transferId', '');

    if ($errors = ApiHelper::checkAttributes([
        'key' => [],
        'transferId' => [],
    ], $request)) {
        return [
            'success' => false,
            'error_code' => 1522,
            'errors' => $errors
        ];
    }

    $transfer = Transfer::where('trid', $trId)
        ->first();

    if (!$transfer || $transfer->fromProject !== $currentProject->id && $transfer->toProject !== $currentProject) {
        $result = [
            'success' => false,
            'error_code' => 1522
        ];

    } else {

        $transactions = Transaction::where('tr_id', $transfer->id)->get();

        $result = [
            'success' => true,
            'fromProject' => Project::getShort($transfer->fromProject),
            'toProject' => Project::getShort($transfer->toProject),
            'fromUser' => $transfer->fromAddress,
            'toUser' => $transfer->toAddress,
            'step' => $transfer->step,
            'amount' => $transfer->amount,
            'status' => $transfer->status,
            'transactions' => $transactions
        ];
    }


    return json_encode($result);
});
