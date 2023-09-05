<?php
header('Content-Type: text/event-stream');
header('Connection: keep-alive');
header('Catch-Control: no-store');

$redis = new Redis();
try {
    $redis->connect('127.0.0.1', 6379);
    $redis->subscribe('sse_channel');
} catch (Throwable $t) {
    echo $t->getMessage();
}

while (true) {
    $message = $redis->psubscribe(['sse_channel'], function ($redis, $pattern, $channel, $message) {
        echo "data: $message\n\n";
        flush();
    });
}