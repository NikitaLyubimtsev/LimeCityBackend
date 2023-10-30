<?php
// TODO Объединить в один файл индекса и настроить роутинг для каждого отдельного файла исполнителя
require_once "SSEServer.php";

$eventId = $_GET['event_id'] ?? null;

if ($eventId && is_numeric($eventId)) {
    try {
        $sse = new SSEServer();
        $sse->markDeliveredEvent($eventId);
        http_response_code(200);
        echo "Success, event $eventId is mark to delivered!";
    } catch (Throwable $t) {
        http_response_code(300);
        echo $t->getMessage();
    }

} else {
    http_response_code(400);
    echo "Missing parameter or non Numeric type of 'event id'";
}