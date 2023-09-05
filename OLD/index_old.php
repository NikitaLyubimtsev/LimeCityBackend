<?php
//set_time_limit(0);
//
//header('Content-Type: text/event-stream');
//header('Connection: keep-alive');
//header('Catch-Control: no-store');
//
//header('Access-Control-Allow-Origin: *');
//
//$redis = new Redis();
//
//try {
//    $redis->connect('127.0.0.1', 6379);
//    $redis->set('name', 'Lime Hello!');
//    $redis->close();
//} catch (Throwable $t) {
//    echo $t->getMessage();
//}
//
//
//$preName = 'Lime Hallo!';
//
//while (true) {
//
//    echo "data: \n\n";
//
//    ob_flush();
//    flush();
//
//
//    if (connection_aborted()) {
//        break;
//    }
//
//
//    try {
//        $name = $redis->get('name');
//    } catch (Throwable $t) {
//        echo $t->getMessage();
//    }
//
//    if ($name !== $preName) {
//        echo 'data: ' . $name . '. Message from Lime, at - ' . date('Y-m-d H:j:s');
//        echo "\n\n";
//
//        ob_flush();
//        flush();
//
//        $preName = $name;
//    }
//
//    sleep(3);
//}
//

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

set_time_limit(0);

header('Content-Type: text/event-stream');
header('Connection: keep-alive');
header('Catch-Control: no-store');

header('Access-Control-Allow-Origin: *');



$redis->hSet('subscrible', 1, 'Sub');
$sub_list = $redis->hGetAll('subscrible');
//$redis->set('message', 'New Msg');

foreach ($sub_list as $key => $value) {
    switch ($value) {
        case 'Sub':
            $data = "Hello to Lime Market";
            break;
        case 'Msg':
            $data = date('Y-m-d H:i:s') . $redis->get('message');
            break;
    }

    echo "data: " . $data . "\n\n";

    ob_flush();
    flush();
}

$redis->hSet('subscrible', 1, 'Msg');

unset($sub_list);

$redis->close();
