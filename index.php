<?php
require 'vendor/autoload.php';
$mapping = require 'mapping.php';
require 'helpers.php';
require 'queue.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Dữ liệu JSON không hợp lệ.']);
    exit;
}

$urls = [
    'hr' => 'http://localhost:5000/api/employees',
    'pr' => 'http://localhost:8000/api/employees',
];

$responses = [];

foreach (['hr', 'pr'] as $target) {
    $mapped = mapData($data, $mapping[$target]);
    $ok = sendToApi($urls[$target], $mapped);
    if (!$ok) {
        pushToQueue($target, $mapped);
        $responses[$target] = 'Đẩy vào queue do lỗi API';
    } else {
        $responses[$target] = 'Gửi API thành công';
    }
}

echo json_encode([
    'message' => 'Dữ liệu đã được xử lý.',
    'status' => $responses
]);
