<?php

use JetBrains\PhpStorm\NoReturn;

require_once 'DataBase.php';

/**
 * Сервер отправки событий сервера
 *
 * Управляет отправкой событий сервера клиентам. Может выполнять массовую отправку или конкретному клиенту. В простейшей реализации требует использование единого, одного общего объекта класса.
 *
 * @package Server-send Event Server
 * @author Lyubimtsev Nikita <LyubimtsevN.A@yandex.ru>
 * @version 1.0.0
 */
class SSEServer
{

    /** @var array Список подключённых клиентов */
    private static array $clients = array();
    /** @var SSEServer|null Единый экземпляр сервера */
    private static ?SSEServer $instance = null;
    /** @var array Список не отправленных сообщений */
    private static array $events = array();
    /** @var int Порядковый номер события для конкретного пользователя пользователей */
    private int $event_id = 0;

    public function __construct()
    {
        set_time_limit(0);
    }

    /**
     * Единственный
     *
     * Создаёт единственный объект класса для эффективной работы
     * @return SSEServer
     */
    public static function getInstance(): SSEServer
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Заголовки для SSEServers
     *
     * Устанавливает необходимые заголовки для отправки сообщений
     *
     * @return void
     */
    public static function setSSEHeaders(): void
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('Access-Control-Allow-Origin: *');
    }

    /**
     * Добавления клиента
     *
     * Добавляет нового клиента в список подключённых к данному каналу
     *
     * @param string $clientId Идентификатор клиента в системе, где используется сервер
     * @return void
     */
    public function addClient(string $clientId): void
    {
        if ((!isset(self::$events[$clientId])) || (!self::$clients[$clientId]['connection'])) {
            self::$clients[$clientId] = ['connection' => true, 'subscriptions' => array()];
            $data = [
                'id' => 0,
                "title" => "Connection",
                'body' => "Success connect client UUID: $clientId",
            ];

            $this->addEvent($clientId, $data);
        }
    }

    /**
     * Добавить событие
     *
     * Добавляет событие в ассоциативный массив <$events> с uuid пользователя в виде ключа и списком событий в виде значения
     *
     * @param string $uuid Идентификатор пользователя для которого предназначено событие
     * @param array $data Ассоциативный массив ["title", "body"]
     * @return void
     */
    public function addEvent(string $uuid, array $data): void
    {
        if ((!isset(self::$clients[$uuid])) || (!self::$clients[$uuid]['connection'])) {
            throw new RuntimeException("Пользователь для которого отправлено событие не обнаружен");
        }
        self::$events[$uuid] = $data;
    }

    /**
     * Удаление клиента
     *
     * @param string $clientId
     * @return void
     */
    public function removeClient(string $clientId): void
    {
        unset(self::$clients[$clientId]);
    }

    /**
     * Подписка клиента на канал
     *
     * @param string $clientId
     * @param string $event
     * @return void
     * @TODO на будущее
     */
    public function subscribeClient(string $clientId, string $event): void
    {
        if (isset(self::$clients[$clientId])) {
            self::$clients[$clientId]['subscription'][] = $event;
        }
    }

    /**
     * Инициализация
     *
     * @return void
     */
    #[NoReturn]
    public function init(): void
    {
        self::setSSEHeaders();

        while (true) {
            if (connection_aborted()) {
                foreach (self::$clients as $clientId => $clientInfo) {
                    if (!is_resource($clientInfo['connection'])) {
                        $this->removeClient($clientId);
                    }
                }
                exit();
            }

            try {
                $this->checkAndAddEvents();

                $this->sendEventToClient();
            } catch (Throwable $t) {
                $this->sendEvent(['title' => 'error', 'body' => $t->getMessage()], "error");
            }

//         //   $this->addEvent('UUID test', ['title' => "New test", 'body' => "test body"]);
//          //  echo "data: " . json_encode(self::$events) . "\n\n";
//
//            $n = rand(1,10000);
//            echo "data: Connection $n. " . json_encode(self::$clients) . "\n\n";
//            ob_flush();
//            flush();

            sleep(10);
        }
    }

    /**
     * Отправить событие клиенту
     *
     * Отправляет событие клиенту по UUID или по ключу <All> из статического массива events
     *
     * @return void
     */
    private function sendEventToClient(): void
    {
       // $this->sendEvent(self::$events);
        foreach (self::$clients as $uuid => $clientInfo) {
            $client_events = array_values(array_filter(self::$events, static fn($item) => ($item === $uuid) || ($item === "all"), ARRAY_FILTER_USE_KEY));

            if (count($client_events) > 1) {
                foreach ($client_events as $data) {
                    $this->sendEvent($data);
                }
            } elseif (count($client_events) === 1) {
                $this->sendEvent($client_events[0]);
            }

            unset(self::$events[$uuid]);
        }
    }

    /**
     * Отправить сообщение
     *
     * Непосредственно отправляет сообщение клиенту
     *
     * @param array $data Ассоциативный массив данных ["id", "title", "body"] отправляемый конечному пользователю в формате JSON
     * @return void
     * TODO продумать пакетную отправку данных когда для пользователя более чем одно событие
     */
    private function sendEvent(array $data): void
    {
        $eventType = "message";
        $cur_event_id = $data['id'] ?? $this->event_id;

        try {
            $this->event_id++;
            $send_data = json_encode($data, JSON_THROW_ON_ERROR);
        } catch (Throwable $t) {
            $send_data = $t->getMessage();
            $eventType = "error";
        }

        echo "id: $cur_event_id\n";
        echo "event: $eventType\n";
        echo "data: $send_data \n\n";

        ob_flush();
        flush();
    }

    /**
     * Проверка и добавление событий
     *
     * Проверяет наличие новых событий в Базе данных и добавление их в массив events в случае обнаружения новых status = 1
     *
     * @return void
     */
    private function checkAndAddEvents(): void
    {
        $conn = new DataBase();
       // while (true) {
        $messages = $conn->getNewEvent();

        if (count($messages) > 0) {
            foreach ($messages as $message) {
                $this->addEvent($message['uuid'], $messages);
            }
        }

        $conn->close();
     //   sleep(5);
        }
 //   }
}