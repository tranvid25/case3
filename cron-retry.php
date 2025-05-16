<?php
require 'vendor/autoload.php';
require 'mapping.php';
require 'helpers.php';
require 'queue.php';

$urls = [
    'hr' => 'http://localhost:5000/api/employees',
    'pr' => 'http://localhost:8000/api/employees'
];

foreach (['hr', 'pr'] as $target) {
    while ($job = popFromQueue($target)) {
        $data = json_decode($job, true);
        $ok = sendToApi($urls[$target], $data);
        if (!$ok) {
            pushToQueue($target, $data);
            // Nếu thất bại thì break, không lấy job tiếp theo
            break;
        }
    }
}
echo "Đã xử lý xong queue.";
