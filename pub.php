<?php
$message = $_POST['message'];
$redis = new Redis();
try {
    $redis->connect('localhost', 6379);
    $redis->setOption(Redis::OPT_READ_TIMEOUT, -1);
} catch (Throwable $t) {
    var_dump($t);
}

try {
    $redis->publish('sse', $message);
    $res = ['code' => 200, 'message' => 'Send'];
} catch (Throwable $t) {
    $res = ['code' => 302, 'message' => 'Not send', 'error' => $t->getMessage()];
    var_dump($t);
}
try {
    echo json_encode($res, JSON_THROW_ON_ERROR | true);
} catch (Throwable $t) {
    echo $t->getMessage();
}
