<?php

use App\Models\ProjectRoad;
use App\Models\Project;
use \App\Http\Helpers\ApiHelper;
use Illuminate\Http\Request;

Route::get('/api/relations', function (Request $request) {

    global $currentProject;

    if ($currentProject->status === 2) {
        return [
            'success' => false,
            'error_code' => 1011,
            'error' => 'Project Blocked'
        ];
    }

    if ($errors = ApiHelper::checkAttributes([
        'key' => [],
    ], $request)) {
        return [
            'success' => false,
            'error_code' => 1522,
            'errors' => $errors
        ];
    }

    $roads = ProjectRoad::where(['status' => 1])
        ->where(function($q) {
            global $currentProject;

            $q->where(['from_project'=>$currentProject->id])
                ->orWhere(['to_project'=>$currentProject->id]);
        })
        ->get()
        ->all();

    $result = [
      'success' => true
    ];

    $map = [];

    $map[$currentProject->id] = $currentProject;

    $getProjectPref = function($id) use (&$map) {
        if (!array_key_exists($id, $map)) {
            $map[$id] = Project::where('id', $id)->first();
        }


        return $map[$id] ? $map[$id]->pref : '';
    };

    $result['items'] = array_map(function($item) use ($getProjectPref) {
        return [
            'from' => $getProjectPref($item->from_project),
            'fromName' => Project::getName($item->from_project),
            'to' => $getProjectPref($item->to_project),
            'toName' => Project::getName($item->to_project),
            'status' => $item->status,
            'fee_strategy' => $item->tax_strategy,
            'min_amount' => $item->min_amount,
            'max_amount' => $item->max_amount,
            'max_day_amount' => $item->max_day_amount,
            'max_month_amount' => $item->max_month_amount,
        ];
    }, $roads);


    return json_encode($result);
});
