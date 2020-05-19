# TelecomCLub
```
Реализовать простой новостной сайт с двумя страницами: список и просмотр новости.

Основные требования
 - реализация должна быть без использования фреймворков и библиотек

Данные:
 - новости: id, категория, название, текст, url картинки, дата публикации
 - категории: можно хранить в коде

Дополнительно:
 - сделать возможность сортировать новости по категории и дате публикации
 - для добавления новостей сделать генератор с консольным или веб интерфейсом
 - в качесте содержания новостей можно использовать Lorem Ipsum или что угодно другое
 - предусмотреть возможность быстрого добавления интерфейса для редактирования новостей

Нагрузка:
 - количество новостей в списке – до 1 000 000
 - устойчивость – 1000 запросов к списку новостей в минуту
 - время открытия страницы списка новостей < 500 мс

Технологии:
 - бэк: PHP, mysql, memcached
 - фронт: на ваше усмотрение

Проект должен быть на гитхабе и отражать процесс разработки.
В результате — ссылка на гитхаб и развёрнутое демо
```

## Available routes

- Main page (`/`)
- Login action (`/?q=login`)
- Logout action (`/?q=logout`)

## Console

`./console generator --limit=1000000 --outputFilePath=/tmp/news_fake.csv`

This command will generate 1000000 rows in DB for News model

## Config

You should setup some variables, if project rolled up in subfolder on a hosting

Open `config/config.php` and fix `baseUrl` and `appUrl`

```php
return [
    'baseUrl' => '/', // Or '/subfolder/'
    'appUrl' => '/', // Or '/subfolder/'
];
```

for connect to database need fill 'db' section in config
```php
return [
    'db' => [
        'dsn' => 'mysql:host=localhost;dbname=dbname',
        'user' => 'dbuser',
        'password' => 'XXX',
    ],
];
```

for using cache need fill 'cache' section in config
```php
return [
    'cache' => [
        'class' => \components\Cache::class,
        'host' => 'localhost',
        'port' => 11211,
    ],
];
```

## Demo
https://dmitry-kapenkin.ru/telecomclub/
