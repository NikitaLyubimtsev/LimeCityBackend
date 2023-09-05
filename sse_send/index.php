<?php

header('Content-Type: text/event-stream');
header('Connection: keep-alive');
header('Catch-Control: no-store');
header('Access-Control-Allow-Origin: *');



function sendEvent($data): void
{
    echo "data: {$data}\n\n";
    ob_flush();
    flush();
}

while (true) {
    $randomNumber = random_int(1, 100);
    sendEvent("Server start: $randomNumber");
    sleep(5);
}


//global $redis;
//$redis = new Redis();
//
//try {
//    $redis->connect('localhost', 6379);
//    $redis->setOption(Redis::OPT_READ_TIMEOUT, -1);
//} catch (Throwable $t) {
//    var_dump($t);
//}
//
//function get_uuid(): array
//{
//    if (!isset($_GET['uuid'])) {
//        throw new Error("Ошибка данных!");
//    }
//    return [$_GET['uuid']];
//}
//
//var_dump($_GET);
//
//
//    try {
//        $redis->subscribe(get_uuid() , function ($redis, $channel, $message) {
//            $res = ['code' => 200, 'message' => 'Subscribe', "data" => "$message on $channel \n\n"];
//            echo json_encode($res, JSON_THROW_ON_ERROR | true);
//            ob_flush();
//            flush();
//        });
//    } catch (Throwable $t) {
//        $res = ['code' => 301, 'message' => 'Not Subscribe', 'error' => $t->getMessage()];
//    }

//header("Content-Type: application/json");
//try {
//    echo json_encode($res, JSON_THROW_ON_ERROR | true);
//} catch (Throwable $t) {
//    var_dump($t);
//}