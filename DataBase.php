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
            $where = " WHERE " . (($conditions) ? implode(" AND ", array_map(static fn($item): string => $item . " = :" . $item ,array_keys($conditions))) : "status = :status");

            $query = $this->connection->prepare("SELECT * FROM event" . $where);

            $query->execute($conditions ?? ['status' => 1]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $t) {
            return ['uuid' => "all", 'title' => "error", 'body' => $t];
        }
    }

    public function updateStatusEvent(int $event_id): void
    {
        $act = $this->connection->prepare("UPDATE message SET status = 0 WHERE id = :id");
        if (!$act->execute(['id' => $event_id])) {
            echo "Error " . $act->errorInfo()[2] . "\n\n";
        }
    }



    public function close(): void
    {
        $this->connection = null;
    }
}