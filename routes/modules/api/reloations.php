<?php

use App\Models\ProjectRoad;
use App\Models\Project;

Route::get('/api/relations', function () {
    $roads = ProjectRoad::where(['status' => 1])
        ->where(function($q) {
            global $currentProject;

            $q->where(['fromProject'=>$currentProject->id])
                ->orWhere(['toProject'=>$currentProject->id]);
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
            'from' => $getProjectPref($item->fromProject),
            'to' => $getProjectPref($item->toProject),
            'fee_strategy' => $item->tax_strategy,
        ];
    }, $roads);


    return json_encode($result);
});
