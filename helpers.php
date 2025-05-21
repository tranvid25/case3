<?php 

use GuzzleHttp\Client;

function splitName($name) {
    $parts = explode(' ', trim($name));
    $first = array_shift($parts);
    $last = implode(' ', $parts);
    return [
        'firstName' => $first,
        'lastName' => $last ?: '',
    ];
}

function prepareData(array $data, array $fields): array {
    $result = [];
    foreach ($fields as $field) {
        $result[$field] = $data[$field] ?? null;
    }
    return $result;
}

// Gán các trường chung, ví dụ employeeId/idEmployee
function mapCommonFields(array &$data) {
    if (isset($data['Employee_ID'])) {
        $data['idEmployee'] = $data['Employee_ID']; // Cho HR
    }
    // Nếu cần thêm mapping trường chung khác thì làm ở đây
}

function mapData($data, $map)
{
    $result = [];
    foreach ($map as $to => $from) {
        if (isset($data[$from])) {
            $result[$to] = $data[$from] ?? null;
        }
    }
    return $result;
}

function sendToApi(string $url, array $data, string $method = 'POST'): bool
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    if ($method !== 'GET' && $method !== 'DELETE') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
        ]);
    } else {
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        echo "Curl error: " . curl_error($ch) . "\n";
    }

    curl_close($ch);

    echo "Request to $url returned HTTP code: $httpCode\n";
    echo "Response: $response\n";

    return $httpCode >= 200 && $httpCode < 300;
}
