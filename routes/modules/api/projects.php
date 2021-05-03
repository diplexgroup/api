<?php

use App\Models\Project;
use App\Models\Wallet;
use \App\Http\Helpers\ApiHelper;
use Illuminate\Http\Request;

Route::get('/api/projects', function (Request $request) {

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

    $withWallet = request()->get('withWallet', '') === 'true';

    $projects = Project::where(['status' => 1])->get()->all();

    $result = [
      'success' => true
    ];

    $result['items'] = array_map(function($item) use ($withWallet) {
        if ($withWallet) {

            $wallet = Wallet::getWallet($item->id, 1, NULL);

            return [
                'name' => $item->name,
                'short' => $item->pref,
                'description' => $item->description,
                'link' => $item->api_front_link,
                'wallet' => $wallet ? $wallet->addr : '',
                'svg' => $item ? $item->svg() : '',
                'show_in_explorer' => $item ? $item->show_in_explorer : '',
            ];

        }

        return [
            'name' => $item->name,
            'short' => $item->pref,
            'description' => $item->description,
            'link' => $item->api_front_link
        ];
    }, $projects);

    if ($withWallet) {
        $burn = Wallet::getBurnWallet();
        $result['burnWallet'] = $burn ? $burn->addr : '';
    }

    return json_encode($result);
});
