<?php

$message = "Hello, world!";

$redis = new Redis();
try {
    $redis->connect('127.0.0.1', 6379);
    $redis->rPush('sse_channel', $message);
} catch (Throwable $t) {
    echo $t->getMessage();
}

