<?php

use App\Models\Transaction;
use Illuminate\Http\Request;
use \App\Models\Transfer;
use \App\Models\Project;
use \App\Models\ProjectRoad;
use \App\Http\Helpers\ApiHelper;

function checkWalletInfo($endpoint, $addr, $token) {

    $url = "$endpoint/get_wallet_info?token=".$token."&wallet=".$addr;


    try {
        $json = file_get_contents($url);

        $data = json_decode($json, true);

        $ok = $data['success'] ?? false;
        $exists = $data['exist'] ?? false;
        $locked = $data['locking'] ?? false;

        return $ok && $exists && !$locked;

    } catch (Exception $ex) {
    }

    return false;
}


Route::post('/api/transfer', function (Request $request) {

    if ($errors = ApiHelper::checkAttributes([
        'amount' => ['regex' => '/^\d+(\.\d+)?$/'],
        'fromUser' => ['regex' => '/^[a-zA-Z\d]{4,}$/'],
        'toUser' => ['regex' => '/^[a-zA-Z\d]{4,}$/'],
        'key' => [],
        'toProj' => ['regex' => '/^[A-Z]{2,4}$/'],
    ], $request)) {
        return [
            'success' => false,
            'error_code' => 1522,
            'errors' => $errors
        ];
    }

    $result = [
      'success' => true
    ];

    $all = $request->all();

    global $currentProject;

    if ($currentProject->status === 2) {
        return [
            'success' => false,
            'error_code' => 1011,
            'error' => 'Project Blocked'
        ];
    }

    //checks
    $toProject = Project::findByPref($all['toProj']);

    // check availability
    $amount = $all['amount'];
    $fromAddress = $all['fromUser'];
    $toAddress = $all['toUser'];
    $error = 0;

    if (!$toProject || $toProject->status === 2) {
        return [
            'success' => false,
            'error_code' => 1012,
            'error' => 'Project Blocked'
        ];
    }


    if ($currentProject->pref !== 'OUT' && !checkWalletInfo($currentProject->api_endpont, $fromAddress, $currentProject->token)) {
        $error = 1001;
    }

    if ($toProject->pref !== 'OUT' && !checkWalletInfo($toProject->api_endpont, $toAddress, $toProject->token)) {
        $error = 1003;
    }


    $road = ProjectRoad::getForTwoProjects($currentProject->id, $toProject->id);

    $err = '';

    if (!$road)  {
        $error = 1005;
        $err = $currentProject->id . ' ---- ' . $toProject->id;
    } else {


        $valueDay = Transfer::getDayValue($currentProject);
        $valueMonth = Transfer::getMonthValue($currentProject);

        if ($amount < $road->min_amount || $amount > $road->max_amount) {
            $err = 'Amount not in ' . $road->min_amount . ' - ' . $road->max_amount . ' DLX';
            $error = 1105;
        } else if ($valueDay + $amount > $road->max_day_amount) {
            $err = 'Day limit exceeded ' . $valueDay . ' + ' . $amount . ' / ' . $road->max_day_amount;
            $error = 1105;
        } else if ($valueMonth + $amount > $road->max_month_amount) {
            $err = 'Month limit exceeded ' . $valueMonth . ' + ' . $amount . ' / ' . $road->max_day_amount;
            $error = 1105;
        } else {
            try {
                $tStrategy = json_decode($road->tax_strategy, true);

                $type = 'calculate' . $tStrategy['type'];

                $feeAmount = $type($road, $tStrategy, $amount);

            } catch (\Exception $ex) {

                $err = 'Wrong tax strategy';
                $error = 1105;

            }
        }


        if (!$error) {
            $trf = Transfer::create($amount, $currentProject, $toProject, $fromAddress, $toAddress, $road, $error);

            if (!$trf) {
                $error = 1106;
            } else {
                $result['transfer_id'] = $trf->trid;
            }
        }
    }

    $result['error_code'] = $error;
    $result['err'] = $err;
    $result['success'] = !$error;


    return json_encode($result);
});
