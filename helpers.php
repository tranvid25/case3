<?php
use GuzzleHttp\Client;

function mapDataToTarget(array $input, array $mapping, string $target): array {
    $result = [];
    foreach ($mapping['fields'] as $key => $targets) {
        if (isset($input[$key]) && !empty($targets[$target])) {
            $result[$targets[$target]] = $input[$key];
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




