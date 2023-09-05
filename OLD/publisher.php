<?php
//$redis = new Redis();
//$redis->connect('127.0.0.1', 6379);

var_dump($_POST);

$message = $_POST['message'] ?? null;
$success = false;

$redis = new Redis();
try {
    $redis->connect('127.0.0.1', 6379);
} catch (Throwable $t) {
    echo $t->getMessage();
}

if ($message) {
    try {
        $redis->rPush('sse_channel', $message);
        $success = true;
    } catch (Throwable $t) {
        $message = $t->getMessage();
    }
}


header("Content-Type: application/json");
try {
    echo json_encode(['success' => $success, 'message' => $message], JSON_THROW_ON_ERROR | true);
    $redis->close();
} catch (Throwable $t) {
    echo $t->getMessage();
}
