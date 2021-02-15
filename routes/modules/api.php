<?php

use App\Http\Middleware\CheckKey;
use App\Http\Middleware\LogRequest;

Route::middleware([CheckKey::class, LogRequest::class])->group(function () {

    foreach (glob(__DIR__ . "/api/*") as $filename) {
        require $filename;
    }

});