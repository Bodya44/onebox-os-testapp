<?php

class OneboxApiClient {

    public function sendRequest($url, $data = array(), $token = false) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $headerArray = array(
            'Content-Type: application'
        );
        if ($token) {
            $headerArray[] = 'token: ' . $token;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = json_decode(@curl_exec($ch), 1);
        curl_close($ch);

        return $response;
    }
}