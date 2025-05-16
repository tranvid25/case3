<?php
use GuzzleHttp\Client;

function mapData($data, $map) {
    $result = [];
    foreach ($map as $to => $from) {
        if (isset($data[$from])) {
            $result[$to] = $data[$from];
        }
    }
    return $result;
}

function sendToApi($url, $data) {
    $client = new Client();
    try {
        $res = $client->post($url, ['json' => $data]);
        return $res->getStatusCode() === 200;
    } catch (Exception $e) {
        error_log("Lỗi gửi API: " . $e->getMessage());
        return false;
    }
}

