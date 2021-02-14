<?php

use App\Models\ProjectRoad;
use  \App\Models\Project;

function calculate1($road, $tStrategy, $amount) {
    $fee_amount = $tStrategy['amount'] ?? 0;
    $percent = $tStrategy['percent'] ?? 0;
    $min = $tStrategy['min'] ?? 0;

    if ($min > $amount) {
        return 0;
    }

    return $percent*$amount + $fee_amount;
}

Route::get('/api/calculate-fee', function () {
    global $currentProject;

    $toProjectPref = request()->get('toProject', '');

    $amount = +request()->get('amount', 0);
    $cId = $currentProject->id;

    $toProject = Project::where('pref', $toProjectPref)->first();


    $road = ProjectRoad::where([
        'status' => 1,
        'fromProject' => $cId,
        'toProject' => $toProject ? $toProject->id : 0
    ])->first();

    $tStrategy = null;
    $feeAmount = 0;
    $err = 'Cannot find transaction direction';
    if ($road && $currentProject->status === 1 && $toProject->status === 1 && $road->status === 1) {

        if ($amount < $road->minAmount || $amount > $road->maxAmount) {
            $err = 'Amount not in ' . $road->minAmount . ' - ' . $road->maxAmount . ' DLX';
        } else {
            try {
                $tStrategy = json_decode($road->tax_strategy, true);

                $type = 'calculate' . $tStrategy['type'];

                $feeAmount = $type($road, $tStrategy, $amount);

            } catch (Exception $ex) {

            }
        }
    } else {
        $err = 'Transaction direction closed';
    }

    $result = [
      'success' => !!$road && !!$tStrategy
    ];

    if ($result['success']) {
        $result['fee'] = $feeAmount;
        $result['burned'] = $feeAmount * $road->burnPercent;
    } else {
        $result['err'] = $err;
    }


    return json_encode($result);
});
