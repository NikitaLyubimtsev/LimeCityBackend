<?php
header('Content-Type: text/event-stream');
header('Connection: keep-alive');
header('Catch-Control: no-store');

$redis = new Redis();
try {
    $redis->connect('127.0.0.1', 6379);
} catch (Throwable $t) {
    echo $t->getMessage();
}


function sendEvent($client, $event, $data): void
{
    $message = "event: $event" . PHP_EOL;
    $message .= "data: $data" . PHP_EOL;
    $message .= PHP_EOL;
    fwrite($client, $message);
    fflush($client);
}

function subscribe($client, $event): void
{
    global $redis;

    $redis->sAdd("subscribers:$event", $client);
}

function unsubscribe($client): void
{
    global $redis;

    $redis->sRem("subscribers:*", $client);
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $event = $_GET['event'] ?? 'default';

    subscribe($client = fopen('php://output', 'w'), $event);
    while (true) {
        $randNum = rand(1,100);
        sendEvent($client, 'newNumber', $randNum);
        sleep(20);
    }
} elseif ($method === 'POST') {
    $event = $_POST['event'] ?? 'default';
    $data = $_POST['data'] ?? null;

    $subscribers = $redis->sMembers("subscribers:$event");
    foreach ($subscribers as $subscriber) {
        sendEvent($subscriber, $event, $data);
    }
} else {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}