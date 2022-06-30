# xml-db-loader

Пример использования:

```
env SQL_SERVERNAME="ip или домен сервера" \
    SQL_USERNAME="ИмяПользователя" \
    SQL_PASSWORD="Пароль" \
    SQL_DBNAME="НазваниеБазыДанных" \
    php -f ./import.php [путь к xml-выгрузке]    
```

Путь по умолчанию "./data_light.php"