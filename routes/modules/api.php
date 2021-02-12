<?php

use App\Http\Middleware\CheckKey;

Route::middleware([CheckKey::class])->group(function () {

    foreach (glob(__DIR__ . "/api/*") as $filename) {
        require $filename;
    }

});