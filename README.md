# Рейсы и счета

Небольшая система учёта рейсов сборных грузов и счетов клиентам.

## Запуск

Нужен PHP 8.2+ с расширением `pdo_sqlite`.

```bash
php bin/reset-db.php
php -S localhost:8000 -t public
```

Открыть http://localhost:8000.

То же через Docker:

```bash
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli \
    sh -c "php bin/reset-db.php && php -S 0.0.0.0:8000 -t public"
```

`php bin/reset-db.php` пересоздаёт базу `data/app.sqlite` из `data/schema.sql` и `data/seed.sql`. Так можно вернуть
исходные данные в любой момент.

## Что внутри

| Папка | Что в ней |
|---|---|
| `public/` | Страницы, API (`public/api/`), скрипты и стили |
| `lib/` | Подключение к базе, общие функции, расчёт счетов |
| `data/` | Схема и данные |
| `bin/` | Служебные скрипты |

## Страницы

| Страница | Адрес | Для чего |
|---|---|---|
| Рейсы | `index.php` | Журнал рейсов: куда, когда, на какой машине, сколько стоит |
| Карточка рейса | `trip.php?id=` | Грузы клиентов в рейсе и доля каждого клиента в стоимости |
| Счета клиентов | `invoices.php` | Счета за месяц: сумма, статус, когда отправлен клиенту и выгружен в 1С. Здесь же счета формируют |
| Счёт | `invoice.php?id=` | Из каких рейсов сложилась сумма счёта |
| Загрузка машин | `load_report.php` | Отчёт для логистов: насколько загружена машина по весу и какую часть веса занимает каждый клиент |
| Выгрузка счетов в 1С | `export_1c.php` | Файл для бухгалтерии: отправленные счета, которые ещё не выгружены |


##
Основные изменения:
- AGENTS.md - автоматически создан opencode
- lib/billing.php
    - добавлена функция calc_share_m3() - расчёт долей по объему; 
    - добавлена функция billing_preview_period() - предпросмотр расчёта счетов
    - добавлена функция billing_apply_period() - расчёт за месяц на основании долей по объёму
- API: 
    - api/billing_apply.php
    - api/billing_preview.php
    - api/trip_split.php
- public/billing.php
- блоки jquery для вызова API:
    - public/js/billing_apply.js
    - public/js/billing_preview.js
    - public/js/trip_split.js
- public/trip.php - добавлена таблица долей рейса, получающая данные из API
- prompts.md - сессия с opencode
