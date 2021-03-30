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
            } else if (isset($rules['regex']) && !preg_match($rules['regex'], $all[$attr])) {
                addError($errors, $attr, 'Wrong regex ' . $rules['regex']);
            }

        }


        return $errors;
    }


    public static function postQuery($url, $data=NULL, $headers = NULL) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if(!empty($data)){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);

        if (curl_error($ch)) {
            trigger_error('Curl Error:' . curl_error($ch));
        }

        curl_close($ch);
        return $response;
    }
}