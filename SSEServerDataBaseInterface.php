<?php

/**
 * Интерфейс базы данных для Server-send Event Server
 *
 * Определяет минимальный набор методов необходимых для реализации работы базы данных с SSEServer
 *
 * @package Server-send Evenet Server
 * @version 1.0.0
 * @author Lyubimtsev Nikita <LyubimtsevN.A@yandex.ru>
 */
interface SSEServerDataBaseInterface
{
    /**
     * Получить новые события
     *
     * Получает новые события из таблицы event в базе данных.
     *
     * @param array|null $conditions Ассоциативный массив параметров запроса ["column_name" => "value"]. Поумалчанию проверяет статусы события и если статус равен 1 (true) то выбирает эти события из базы данных.
     * @return array Итоговый массив строк из базы данных
     * @TODO продумать, может не статусы а параметр is_received (Подучено?)
     */
    public function getNewEvent(?array $conditions = null): array;

    /**
     * Обновление статуса события
     *
     * Обновляет статус события после получения на стороне клиента. Необходимо реализовать метод подтверждения о получении события клиентом.
     *
     * @param int $event_id
     * @return void
     */
   public function updateStatusEvent(int $event_id): void;
}