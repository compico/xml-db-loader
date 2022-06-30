<?php

include_once("../classes/AutoCatalog.php");
include_once("../classes/CatalogDatabase.php");
include_once("../vendor/phpunit/phpunit/src/Framework/TestCase.php");

use PHPUnit\FrameWork\TestCase;

class TestCatalogDatabase extends TestCase {
    public function test_merge_data(){
        $SQL_SERVERNAME = getenv("SQL_SERVERNAME");
        $SQL_USERNAME   = getenv("SQL_USERNAME");
        $SQL_PASSWORD   = getenv("SQL_PASSWORD");
        $SQL_DBNAME     = getenv("SQL_DBNAME");

        try {
            $data[] = new AutoCatalog("../test_data/data1.xml", true);
            $data[] = new AutoCatalog("../test_data/data2.xml", true);
            $data[] = new AutoCatalog("../test_data/data3.xml", true);
            $catalog_db = new CatalogDatabase($SQL_SERVERNAME,$SQL_USERNAME,$SQL_PASSWORD,$SQL_DBNAME, "TestOffers");

            $catalog_db->conn->query("TRUNCATE `TestOffers`");

            printf("Тест первый - добавление данных\n\n");
            $stat = $catalog_db->merge_data($data[0]);
            printf("\033[94m| Пропущено: %d,\n\033[92m| Добавлено: %d,\n\033[96m| Обновлено: %d,\n\033[91m| Удалено: %d,\033[39m \n\n",
                $stat["skipped"], $stat["added"], $stat["updated"], $stat["deleted"]);
            $this->assertSame(0, $stat["skipped"]);
            $this->assertSame(3, $stat["added"]);
            $this->assertSame(0, $stat["updated"]);
            $this->assertSame(0, $stat["deleted"]);

            printf("Тест второй - удаление и замена данных\n\n");
            $stat = $catalog_db->merge_data($data[1]);
            printf("\033[94m| Пропущено: %d,\n\033[92m| Добавлено: %d,\n\033[96m| Обновлено: %d,\n\033[91m| Удалено: %d,\033[39m \n\n",
                $stat["skipped"], $stat["added"], $stat["updated"], $stat["deleted"]);
            $this->assertSame(2, $stat["skipped"]);
            $this->assertSame(1, $stat["added"]);
            $this->assertSame(0, $stat["updated"]);
            $this->assertSame(1, $stat["deleted"]);

            printf("Тест третий - обновление данных\n\n");
            $stat = $catalog_db->merge_data($data[2]);
            printf("\033[94m| Пропущено: %d,\n\033[92m| Добавлено: %d,\n\033[96m| Обновлено: %d,\n\033[91m| Удалено: %d,\033[39m \n\n",
                $stat["skipped"], $stat["added"], $stat["updated"], $stat["deleted"]);
            $this->assertSame(1, $stat["skipped"]);
            $this->assertSame(0, $stat["added"]);
            $this->assertSame(2, $stat["updated"]);
            $this->assertSame(0, $stat["deleted"]);

            $catalog_db->conn->query("TRUNCATE `TestOffers`");

            $catalog_db->conn->close();
        } catch (\Throwable $th) {
            printf("Error:\n%s\n", $th);
        }
    }
}