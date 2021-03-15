<?php

namespace App\Http\Helpers;

class ApiHelper {

    public static function checkAttributes($checkFor, $request) {

        $errors = [];

        $all = $request->all();

        function addError(&$errors, $attr, $error) {
            if ($errors[$attr]) {
                $errors[$attr] = [$error];
            } else {
                $errors[$attr] []= $error;
            }
        }

        foreach ($checkFor as $attr => $rules) {

            if (!isset($all[$attr])) {
                addError($errors, $attr, 'Not Exists');
            }


        }


        return $errors;
    }

}