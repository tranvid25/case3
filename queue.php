<?php
require 'vendor/autoload.php';
$redis = new Predis\Client();

function pushToQueue($target, $data) {
    global $redis;
    $redis->rpush("queue:$target", json_encode($data));
}

function popFromQueue($target) {
    global $redis;
    return $redis->lpop("queue:$target");
}
