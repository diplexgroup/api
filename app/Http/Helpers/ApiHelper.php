<?php

namespace App\Http\Helpers;

class ApiHelper {

    public static function checkAttributes($checkFor, $request) {

        $errors = [];

        $all = $request->all();

        function addError(&$errors, $attr, $error) {
            if (!isset($errors[$attr])) {
                $errors[$attr] = [$error];
            } else {
                $errors[$attr] []= $error;
            }
        }

        foreach ($checkFor as $attr => $rules) {
 
            if (!isset($all[$attr])) {
                addError($errors, $attr, 'Not Exist');
            } else if (isset($rules['regex']) && preg_match($rules['regex'], $all[$attr])) {
                addError($errors, $attr, 'Wrong regex ' . $rules['regex']);
            }

        }


        return $errors;
    }

}