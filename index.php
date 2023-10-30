<?php
/**
 * @param int $errno
 * @param string $errstr
 * @param string|null $errfile
 * @param int|null $errline
 * @return bool
 * @TODO доработать
 */
function handler(int $errno, string $errstr, ?string $errfile = null, ?int $errline = null): bool
{
    switch ($errno) {
        case E_USER_ERROR:
            $data = [
                "info" => "Пользовательская ОШИБКА [$errno] $errstr",
                "phpInfo" => "PHP " . PHP_VERSION . " (" . PHP_OS . ")"
            ];
            break;
        case E_USER_WARNING:
            $data = [
                'info' => "Пользовательское ПРЕДУПРЕЖДЕНИЕ: [$errno] $errstr",
            ];
            break;
        default:
            $data = [
                'info' => "Неизвестная ошибка: [$errno] $errstr",
            ];
    }
    $info = json_encode($data);

    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    header('Access-Control-Allow-Origin: *');
    echo "data: " . $info . "\n\n";
    return true;
}

set_error_handler('handler', E_ALL);

require_once 'SSEServer.php';

$SSE = SSEServer::getInstance();

$uuid = $_GET['uuid'] ?? null;
//$is_delivered = $_GET['is_delivered'] ?? false;

if ($uuid) {
  //  if ($is_delivered) {
   //     $SSE->markDeliveredEvent($GET['event_id'] ?? null);
   // }
    $SSE->addClient($uuid);
    $SSE->init();
}

//$sse->subscribeClient($uuid, $uuid);


