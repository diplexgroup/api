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


    static function checkUserIp() {
        $user = Auth::user();

        if (!$user || !$user->ip || !strlen($user->ip) || $user->ip === self::getIp()) {
            return true;
        }

        return false;
    }


    static function checkProjectIp($project) {
        if (!strlen($project->ip) || ($project->ip === self::getIp())) {
            return true;
        } else {
            return false;
        }
    }
}
