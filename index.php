<?php
require 'vendor/autoload.php';
$mapping = require 'mapping.php';
require 'helpers.php';
require 'queue.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

$urls = [
    'hr' => 'http://localhost:5000/api/employees',
    'pr' => 'http://localhost:8000/api/employees',
];

$responses = [];

switch ($method) {
    case 'POST':
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu JSON không hợp lệ.']);
            exit;
        }
        foreach (['hr', 'pr'] as $target) {
            $mapped = mapData($data, $mapping[$target]);
            $ok = sendToApi($urls[$target], $mapped, 'POST');
            if (!$ok) {
                pushToQueue($target, $mapped);
                $responses[$target] = 'Đẩy vào queue do lỗi API';
            } else {
                $responses[$target] = 'Tạo mới thành công';
            }
        }
        break;

    case 'PUT':
case 'PATCH':
    $id = $data['id'] ?? ($_GET['id'] ?? null);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Thiếu trường "id" để cập nhật.']);
        exit;
    }
    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['error' => 'Dữ liệu JSON không hợp lệ.']);
        exit;
    }

    foreach (['hr', 'pr'] as $target) {
        $mapped = mapData($data, $mapping[$target]);
        $urlWithId = $urls[$target] . '/' . $id;
        $ok = sendToApi($urlWithId, $mapped, $method);  // Giữ đúng method PUT hoặc PATCH

        if (!$ok) {
            pushToQueue($target, array_merge(['id' => $id], $mapped));
            $responses[$target] = 'Cập nhật thất bại, đã vào queue';
        } else {
            $responses[$target] = 'Cập nhật thành công';
        }
    }
    break;
    case 'DELETE':
    // Hỗ trợ cả body JSON lẫn query string
    $id = $data['id'] ?? ($_GET['id'] ?? null);

    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Thiếu trường "id" để xóa.']);
        exit;
    }

    foreach (['hr', 'pr'] as $target) {
        $urlWithId = $urls[$target] . '/' . $id;
        $ok = sendToApi($urlWithId, [], 'DELETE');
        if (!$ok) {
            pushToQueue($target, ['id' => $id, '_delete' => true]);
            $responses[$target] = 'Xóa thất bại, đã vào queue';
        } else {
            $responses[$target] = 'Xóa thành công';
        }
    }
    break;


    default:
        http_response_code(405);
        echo json_encode(['error' => 'Phương thức không được hỗ trợ.']);
        exit;
}

echo json_encode([
    'message' => 'Đã xử lý yêu cầu.',
    'method' => $method,
    'status' => $responses
]);
