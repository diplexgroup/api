<?php

use App\Models\ProjectRoad;
use App\Models\Project;
use \App\Http\Helpers\ApiHelper;
use Illuminate\Http\Request;

Route::get('/api/relations', function (Request $request) {


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
                ->orWhere(['from_project'=>$currentProject->id]);
        })
        ->get()
        ->all();

    $result = [
      'success' => true
    ];

    $map = [];

    global $currentProject;
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
            'to' => $getProjectPref($item->to_project),
            'fee_strategy' => $item->tax_strategy,
            'min_amount' => $item->min_amount,
            'max_amount' => $item->max_amount,
            'max_day_amount' => $item->max_day_amount,
            'max_month_amount' => $item->max_month_amount,
        ];
    }, $roads);


    return json_encode($result);
});
