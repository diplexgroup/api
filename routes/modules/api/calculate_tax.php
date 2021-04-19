<?php

use App\Models\ProjectRoad;
use \App\Models\Project;
use \App\Models\Transaction;
use \App\Models\Transfer;
use \App\Http\Helpers\ApiHelper;
use Illuminate\Http\Request;

function calculate1($road, $tStrategy, $amount) {
    $fee_amount = $tStrategy['amount'] ?? 0;
    $percent = $tStrategy['percent'] ?? 0;
    $min = $tStrategy['min'] ?? 0;

    if ($min > $amount) {
        return 0;
    }

    return $percent*$amount + $fee_amount;
}

Route::get('/api/calculate-fee', function (Request $request) {

    global $currentProject;

    if ($currentProject->status === 2) {
        return [
            'success' => false,
            'error_code' => 1011,
            'error' => 'Project Blocked'
        ];
    }

    if ($errors = ApiHelper::checkAttributes([
        'toProject' => ['regex' => '/^[A-Z]{2,6}$/'],
        'amount' => ['regex' => '/^\d+(\.\d+)?$/'],
        'key' => [],
    ], $request)) {
        return [
            'success' => false,
            'error_code' => 1522,
            'errors' => $errors
        ];
    }

    $toProjectPref = request()->get('toProject', '');

    $amount = +request()->get('amount', 0);
    $cId = $currentProject->id;

    $toProject = Project::where('pref', $toProjectPref)->first();

    if (!$toProject || $toProject->status === 2) {
        return [
            'success' => false,
            'error_code' => 1012,
            'error' => 'Project Blocked'
        ];
    }

    $road = ProjectRoad::where([
        'status' => 1,
        'from_project' => $cId,
        'to_project' => $toProject ? $toProject->id : 0
    ])->first();


    if (!$road || $road->status === 2) {
        return [
            'success' => false,
            'error_code' => 1005,
            'error' => 'Road Blocked'
        ];
    }

    $tStrategy = null;
    $feeAmount = 0;
    $err = 'Cannot find transaction direction';

    $valueDay = Transfer::getDayValue($currentProject);
    $valueMonth = Transfer::getMonthValue($currentProject);



    if ($road && $currentProject->status === 1 && $toProject->status === 1 && $road->status === 1) {
        $result['max_amount'] = $road->max_amount;
        $result['min_amount'] = $road->min_amount;
        $result['max_month_amount'] = $road->max_month_amount;
        $result['max_day_amount'] = $road->max_day_amount;
        $result['cur_day_value'] = $valueDay;
        $result['cur_month_value'] = $valueMonth;
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

    $result['success'] = !!$road && !!$tStrategy;

    if ($result['success']) {
        $result['fee'] = round($feeAmount, 6);
        $result['burned'] = round($feeAmount * $road->burn_percent, 6);
    } else {
        $result['err'] = $err;
        $result['error_code'] = 1106;
    }


    return json_encode($result);
});
