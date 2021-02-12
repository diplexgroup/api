<?php

use App\Models\ProjectRoad;

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

    $result['items'] = array_map(function($item) {
        return [
            'from' => $item->fromProject,
            'to' => $item->toProject,
            'fee_strategy' => $item->tax_strategy,
        ];
    }, $roads);


    return json_encode($result);
});
