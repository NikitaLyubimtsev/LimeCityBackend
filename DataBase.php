<?php
require_once "SSEServerDataBaseInterface.php";

class DataBase implements SSEServerDataBaseInterface
{
    private string $host = '45.147.179.182';
    private string $user = 'itprorab';
    private string $password = 'ItcwInI7z%WW';
    private string $port = '3306';
    private string $dbname = 'itprorab';
    private ?PDO $connection;

    public function __construct()
    {
        $dsn = "mysql:host=$this->host;port=$this->port;dbname=$this->dbname";
        try {
            $this->connection = new PDO($dsn, $this->user, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Connection success!" . PHP_EOL;
        } catch (Throwable $t) {
            echo "Connection failed: " . $t->getMessage() . "\n";
        }
    }

    public function getNewEvent(?array $conditions = null): array
    {
        try {
            $where = " WHERE " . (($conditions) ? implode(" AND ", array_map(static fn($item): string => $item . " = :" . $item ,array_keys($conditions))) : "delivered_status = :delivered_status");

            $query = $this->connection->prepare("SELECT * FROM event" . $where);

            $query->execute($conditions ?? ['delivered_status' => 0]);
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $t) {
            $data = ['uuid' => "all", 'title' => "error", 'body' => $t];
        }
        return $data;
    }

    public function updateSendStatusEvent(int $event_id): void
    {
        $act = $this->connection->prepare("UPDATE event SET send_status = 1 WHERE id = :id");
        $act->execute(['id' => $event_id]);
    }

    public function updateDeliveredStatus(int $event_id): void
    {
        $act = $this->connection->prepare("UPDATE event SET delivered_status = 1 WHERE id = :id");
        $act->execute(['id' => $event_id]);
    }

    /**
     * Добавление в базу данных нового сообщения
     *
     * @param array $data Состав массива с обязательной последовательностью [uuid, title, body]
     * @return void
     */
    public  function addNewEvent(array $data): void
    {
        $conn = $this->connection;
        $act = $conn->prepare("INSERT INTO event (uuid, title, body) VALUES (:uuid, :title, :body)");
        $act->execute($data);
    }

    public function close(): void
    {
        $this->connection = null;
    }
}