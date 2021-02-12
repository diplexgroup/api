<?php

use App\Models\ProjectRoad;

function calculate1($tStrategy, $amount) {
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

    $project = +request()->get('project', 0);
    $direction = +request()->get('direction', 1);

    $cId = $currentProject->id;

    if ($direction === 2) {
        [$cId, $project] = [$project, $cId];
    }

    $road = ProjectRoad::where([
        'status' => 1,
        'fromProject' => $cId,
        'toProject' => $project
    ])->first();

    $tStrategy = null;
    $feeAmount = 0;
    try {
        $tStrategy = json_decode($road->tax_strategy, true);

        $amount = +request()->get('amount', 0);

        $type = 'calculate'.$tStrategy['type'];

        $feeAmount = $type($tStrategy, $amount);

    } catch(Exception $ex) {

    }


    $result = [
      'success' => !!$road && !!$tStrategy
    ];

    if ($result['success']) {
        $result['fee'] = $feeAmount;
    }


    return json_encode($result);
});
