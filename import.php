<?php

include_once("classes/AutoCatalog.php");
include_once("classes/CatalogDatabase.php");

// Проверка аргументов
isset($argv[1]) ? $filepath = $argv[1] : $filepath = "./data_light.xml";

//\033[31m

$SQL_SERVERNAME = getenv("SQL_SERVERNAME");
$SQL_USERNAME   = getenv("SQL_USERNAME");
$SQL_PASSWORD   = getenv("SQL_PASSWORD");
$SQL_DBNAME     = getenv("SQL_DBNAME");

if ($SQL_DBNAME == false) {
    printf("\nПример использования:\n\"env SQL_SERVERNAME=\"ip или домен сервера\" \\\nSQL_USERNAME=\"ИмяПользователя\" \\\nSQL_PASSWORD=\"Пароль\" \\\nSQL_DBNAME=\"НазваниеБазыДанных\"\" \\\nphp -f ./import.php [путь к xml-выгрузке]\"\n\nПуть по умолчанию \"./data_light.php\"");
    return;
}

try {
    $auto_catalog = new AutoCatalog($filepath, true);
    $catalog_db = new CatalogDatabase($SQL_SERVERNAME,$SQL_USERNAME,$SQL_PASSWORD,$SQL_DBNAME, "Offers");
    $stat = $catalog_db->merge_data($auto_catalog);
    printf("\n\033[94m| Пропущено: %d,\n\033[92m| Добавлено: %d,\n\033[96m| Обновлено: %d,\n\033[91m| Удалено: %d,\033[39m \n\n",
        $stat["skipped"], $stat["added"], $stat["updated"], $stat["deleted"]);
    $catalog_db->conn->close();
} catch (\Throwable $th) {
    printf("Error:\n%s\n", $th);
}

