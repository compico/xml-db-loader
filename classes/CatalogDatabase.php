<?php

include_once("AutoCatalog.php");

class CatalogDatabase
{
    public mysqli $conn;

    // Сделал отдельное поле заранее
    // Так как эта класс будет использоватся в тестах
    // И для тестов создаётся отдельная таблица в базе данных
    public string $table;

    public function __construct(string $servername, string $username, string $password, string $dbname, string $table)
    {
        $this->conn = new mysqli($servername, $username, $password, $dbname);
        if ($this->conn->connect_error) {
            die("Connection to MySQL failed: " . $this->conn->connect_error);
        }
        $this->table = $table;
    }

    public function merge_data(AutoCatalog $data): array
    {
        // Переменная для статистики
        $stat = array("skipped" => 0, "updated" => 0, "added" => 0, "deleted" => 0);
        $hash = $data->get_id_index_pair();

        // Получаем массив id элементов xml'а
        $ids = $data->get_id_array();
        while ($this->conn->next_result()) $this->conn->store_result();

        // Удаляем offers если нет в xml-выгрузке
        if ($this->conn->query(sprintf("DELETE FROM %s WHERE `id` NOT IN (%s);", $this->table, implode(", ", $ids)))) {
            $stat["deleted"] += $this->conn->affected_rows;
        } else {
            throw new Exception($this->conn->error);
        }

        //Обновляем данные, если есть изменения в xml-выгрузке
        $result = $this->conn->query(sprintf("SELECT * FROM %s WHERE `id` IN (%s);", $this->table, implode(", ", $ids)));
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                while ($this->conn->next_result()) $this->conn->store_result();
                $for_diff_el = array();
                $diff = array_diff($data->offer_to_array($data->xml->offers->offer[$hash[$row["id"]]]), $row);
                if (count($diff) == 0) {
                    $stat["skipped"]++;
                    continue;
                }
                $this->conn->query(sprintf("UPDATE %s SET %s WHERE `id`=%d;", $this->table, $this->array_to_set_string($diff), $row["id"]));
                $stat["updated"]++;
            }
        }
        // Получаем все id из каждой строки таблицы
        $result = $this->conn->query(sprintf("SELECT `id` from %s;", $this->table));

        // Если таблица пуста, просто добавляем всё что имеется.
        if ($result->num_rows == 0) {
            while ($this->conn->next_result()) $this->conn->store_result();
            $sql = "";
            foreach ($data->xml->offers->offer as $value) {
                $sql .= sprintf("INSERT INTO %s %s;", $this->table, $this->array_to_insert_string($data->offer_to_array($value)));
            }
            $this->conn->multi_query($sql);
            $stat["added"] += $data->xml->offers->offer->count();
        }

        // Если таблица не пуста, смотрим, какие элементы отсутствует в базе
        if ($result->num_rows > 0) {
            $db_ids = array();
            while ($row = $result->fetch_assoc()) {
                while ($this->conn->next_result()) $this->conn->store_result();
                $db_ids[] = $row["id"];
            }
            $diff = array_diff($ids, $db_ids);

            $sql = "";
            foreach ($diff as $id) {
                $sql .= sprintf("INSERT INTO %s %s;",
                    $this->table,
                    $this->array_to_insert_string(
                        $data->offer_to_array(
                            $data->xml->offers->offer[$hash[$id]]
                        )));
            }
            if ($sql != "")
            $this->conn->multi_query($sql);
            $stat["added"] += count($diff);
        }
        return $stat;
    }

    // Перевод массива строку для UPDATE запроса SQL
    function array_to_set_string(array $el): string
    {
        $x = array();
        foreach ($el as $key => $value) {
            $x[] = sprintf("`%s`=%s", $key, gettype($value) == "string" ? "'$value'" : "$value");
        }
        return implode(", ", $x);
    }

    // Перевод массива строку для INSERT запроса SQL
    function array_to_insert_string(array $el): string
    {
        $x = array();
        $keys = array();
        foreach ($el as $key => $value) {
            $keys[] = $key;
            $x[] = sprintf("%s", gettype($value) == "string" ? "'$value'" : "$value");
        }
        return sprintf("(`%s`) VALUES (%s)", implode("`, `", $keys), implode(", ", $x));
    }
}