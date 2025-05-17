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

function sendToApi($url, $data, $method = 'POST') {
    $client = new \GuzzleHttp\Client();
    try {
        $res = $client->request($method, $url, ['json' => $data]);
        $status = $res->getStatusCode();
        if ($status >= 200 && $status < 300) {
            return true;
        } else {
            error_log("API trả về status code không thành công: $status, body: " . $res->getBody());
            return false;
        }
    } catch (\GuzzleHttp\Exception\RequestException $e) {
        error_log("Lỗi gửi request đến API: " . $e->getMessage());
        if ($e->hasResponse()) {
            error_log("Response body: " . $e->getResponse()->getBody());
        }
        return false;
    } catch (Exception $e) {
        error_log("Lỗi khác: " . $e->getMessage());
        return false;
    }
}




