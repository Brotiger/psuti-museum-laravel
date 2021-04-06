Для локальной разработки

1) vi app/Providers/AppServiceProvider

2) закоментировать 28 строку '\URL::forceScheme('https');'

3) vi app/config/app.php

4) изменить в 55 строке ''url' => env('APP_URL', 'https://localhost'),' https соединение на http

5) docker-compose up

6) переименовать .env.local в .env

7) php artisan serve