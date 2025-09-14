<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

🚖 RideTech API

RideTech — это REST API сервис для организации поездок с поддержкой ролей (пассажир / водитель), управления машинами, заказами поездок и отзывами.
Проект написан на Laravel 11 + Sanctum с архитектурой Controller → Service → Model.

 Функционал

  Регистрация и аутентификация (Laravel Sanctum)

  Роли пользователей: passenger и driver

  Управление машинами (только для водителей)

  Управление поездками (создание, принятие, завершение)

  Система отзывов о водителях

  WebSockets (Broadcasting через Reverb)

  Redis для кеша

 PostgreSQL для БД

  Установка и запуск
1. Клонировать репозиторий
git clone https://github.com/yourname/ridetech-api.git
cd ridetech-api

2. Установить зависимости
composer install
npm install && npm run build

3. Скопировать .env и настроить
cp .env.example .env

4. Сгенерировать ключ приложения
php artisan key:generate

5. Запустить миграции и сидеры
php artisan migrate --seed

6. Запустить сервер
php artisan serve


API доступно по адресу:

http://localhost:8000/api/v1

  Примеры API
  Аутентификация
Регистрация
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{
    "name":"John Doe",
    "phone":"+79998887766",
    "password":"secret123",
    "role":"passenger"
  }'

Логин
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"phone":"+79998887766","password":"secret123"}'


Ответ:

{
  "token": "1|abc123..."
}

Логаут
curl -X POST http://localhost:8000/api/v1/logout \
  -H "Authorization: Bearer 1|abc123..."

  Trips (Поездки)
Создать поездку (пассажир)
curl -X POST http://localhost:8000/api/v1/trips \
  -H "Authorization: Bearer 1|abc123..." \
  -H "Content-Type: application/json" \
  -d '{
    "from":"Москва",
    "to":"Санкт-Петербург",
    "price":1500
  }'

Получить список поездок пользователя
curl -X GET http://localhost:8000/api/v1/trips \
  -H "Authorization: Bearer 1|abc123..."

Принять поездку (водитель)
curl -X POST http://localhost:8000/api/v1/trips/1/accept \
  -H "Authorization: Bearer 1|driverToken..."

 Cars (Машины — только для водителей)
Добавить машину
curl -X POST http://localhost:8000/api/v1/cars \
  -H "Authorization: Bearer 1|driverToken..." \
  -H "Content-Type: application/json" \
  -d '{
    "make":"Toyota",
    "model":"Camry",
    "plate":"A123BC77"
  }'

Список машин водителя
curl -X GET http://localhost:8000/api/v1/cars \
  -H "Authorization: Bearer 1|driverToken..."

Удалить машину
curl -X DELETE http://localhost:8000/api/v1/cars/1 \
  -H "Authorization: Bearer 1|driverToken..."

  Reviews (Отзывы)
Оставить отзыв (пассажир → водитель)
curl -X POST http://localhost:8000/api/v1/reviews/2 \
  -H "Authorization: Bearer 1|passengerToken..." \
  -H "Content-Type: application/json" \
  -d '{
    "rating":5,
    "comment":"Отличная поездка, водитель вежливый!"
  }'

Получить отзывы о водителе
curl -X GET http://localhost:8000/api/v1/reviews/2 \
  -H "Authorization: Bearer 1|anyToken..."



Полезные команды
php artisan migrate:fresh --seed   # пересоздать БД с данными
php artisan cache:clear            # очистить кэш
php artisan config:clear           # сбросить конфигурацию
php artisan route:list             # список маршрутов

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
