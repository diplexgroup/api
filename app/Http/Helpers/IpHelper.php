<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Auth;

class IpHelper {

    static function getIp() {
        $ip = NULL;

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }


    static function checkIp() {
        $user = Auth::user();

        if (!$user || !$user->ip || $user->ip === self::getIp()) {
            return true;
        }

        return false;
    }


}