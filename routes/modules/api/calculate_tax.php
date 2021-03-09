<?php

use App\Models\ProjectRoad;
use  \App\Models\Project;
use  \App\Models\Transaction;

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
        'from_project' => $cId,
        'to_project' => $toProject ? $toProject->id : 0
    ])->first();

    $tStrategy = null;
    $feeAmount = 0;
    $err = 'Cannot find transaction direction';

    $valueDay = Transaction::getDayValue($currentProject);
    $valueMonth = Transaction::getMonthValue($currentProject);


    if ($road && $currentProject->status === 1 && $toProject->status === 1 && $road->status === 1) {

        if ($amount < $road->min_amount || $amount > $road->max_amount) {
            $err = 'Amount not in ' . $road->min_amount . ' - ' . $road->max_amount . ' DLX';
        } else if ($valueDay + $amount > $road->max_day_amount) {
            $err = 'Day limit exceeded ' . $valueDay . ' + ' . $amount . ' / ' .  $road->max_day_amount;
        } else if ($valueMonth + $amount > $road->max_month_amount) {
            $err = 'Month limit exceeded ' . $valueMonth . ' + ' . $amount . ' / ' .  $road->max_day_amount;
        } else {
            try {
                $tStrategy = json_decode($road->tax_strategy, true);

                $type = 'calculate' . $tStrategy['type'];

                $feeAmount = $type($road, $tStrategy, $amount);

            } catch (Exception $ex) {

                $err = 'Wrong tax strategy';

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
        $result['error_code'] = 1101;
    }


    return json_encode($result);
});
