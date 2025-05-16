<?php
require 'vendor/autoload.php'; // load thư viện Predis

try {
    $redis = new Predis\Client([
        'scheme' => 'tcp',
        'host'   => '127.0.0.1',
        'port'   => 6379,
    ]);
    echo $redis->ping();  // In ra PONG nếu kết nối thành công
} catch (Exception $e) {
    echo "Không kết nối được Redis: " . $e->getMessage();
}
