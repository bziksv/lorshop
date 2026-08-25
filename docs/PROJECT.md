# lorshop.ru — документация проекта

Bitrix-интернет-магазин LOR-оборудования и инструментов (поставки медтехники по РФ).

Шаблон: **medical-templates**. Компоненты: **nbrains**, **niges**. Модули: **prime.alerts** (`local/`), **prime.cleaner**, **prime.roistatbitrixcms**, **prime.updateprice**, **artrix.imageoptimizer**, **arturgolubev.***, **isaev.seotemplate**, **kda.exportexcel**, **delight.webpconverter**, **imyie.orderminprice**, **asd.iblock**, **niges.cookiesaccept**.

## Репозиторий и окружения

| | |
|---|---|
| GitHub | https://github.com/bziksv/lorshop |
| **Git root** | `lorshop.ru/` (= корень сайта на prod) |
| Prod IP | `45.90.35.63` |
| Prod path | `/var/www/lorshop.ru/data/www/lorshop.ru` |
| Домен | https://lorshop.ru |
| Локально | http://127.0.0.1:8098/ |

Родительская папка `lorshop/` — обёртка Mac: дамп БД (`lorshop_ru_db.sql`), `.cursorignore`, корневой `README.md`.

## Структура

```
lorshop/                         # workspace (Mac)
├── lorshop_ru_db.sql            # дамп БД (не в git)
├── .cursorignore
├── README.md
└── lorshop.ru/                  # корень сайта
    ├── .local/                  # nginx/php-fpm (Mac, soft)
    ├── docs/                    # документация
    ├── scripts/                 # dev
    ├── bitrix/
    │   ├── modules/             # ядро + сторонние/кастомные
    │   ├── components/{nbrains,niges}/
    │   ├── templates/medical-templates/
    │   └── php_interface/       # dbconn, init.php
    ├── local/modules/prime.alerts/
    ├── catalog/ personal/ about/ …
    └── upload/                  # медиа (не в git)
```

## База данных

| Параметр | Prod (из дампа/конфига) | Локально |
|----------|-------------------------|----------|
| Host | `localhost` | `127.0.0.1` |
| Database | `lorshop_ru_db` | `lorshop_ru_db` |
| User | `lorshop_ru_usr` | `lorshop_local` |
| Password | (prod) | `lorshop_local` |

Дамп: MariaDB 10.3 → импорт в Homebrew MySQL 8.0 (`/tmp/mysql.sock`).

Инфоблок каталога: `IBLOCK_CATALOG = 33` (`bitrix/php_interface/init.php`).

Сайт в БД: `LID=s1`, `SERVER_NAME=lorshop.ru`.

## Локальная разработка (Mac, soft)

Порты: **8098** (nginx), **9098** (php-fpm). MySQL 3306 (Homebrew).

```bash
cd lorshop.ru
cp .local/db.env.example .local/db.env   # один раз
./scripts/setup-local-db.sh --background # один раз, щадящий импорт
./scripts/start-dev.sh
./scripts/stop-dev.sh
```

Soft-режим:

- php-fpm `ondemand`, max **2** workers
- `memory_limit` 512M, opcache 64M
- импорт дампа в фоне (`--background`), без параллельной нагрузки

`apply-local-db-config.sh` пишет `dbconn.local.php` и правит `.settings.php` под локальные креды; prod-копии кладёт в `.local/backup/`.

### Занятые порты (соседние проекты)

| Порт | Проект |
|------|--------|
| 8080 | almamed |
| 8082 | vilmed |
| 8084 | polimer |
| 8085 | lormag |
| 8086 | metplus-vrn |
| 8087 | oftalmag |
| 8088 | metprof-vrn |
| 8090 | medplakaty |
| 8093 | dckljaksa |
| 8094 | fasad36 |
| 8095 | gnkmed |
| 8096 | gorexpert |
| 8097 | kawe |
| **8098** | **lorshop** |
| **9098** | **lorshop php-fpm** |

## Разделы сайта

| Путь | Назначение |
|------|------------|
| `/` | Главная (слайдер, популярные категории, новости) |
| `/catalog/` | Каталог |
| `/personal/` | ЛК, корзина `/personal/cart/`, заказ `/personal/order/` |
| `/about/` `/dostavka/` `/oplata/` `/kontakty/` | Инфо |
| `/news/` `/articles/` | Контент |
| `/auth/` `/login/` | Авторизация |
| `/bitrix/admin/` | Админка Bitrix |

## Кастом / заметки

- `hand1CtoSite.php` — обмен с 1С
- `prime.alerts` — политика email/алертов (`local/modules/`)
- В `dbconn.php` на prod жёстко `SERVER_PORT=443` — локально перекрывается `dbconn.local.php`
- Композит/html_pages при старте soft-стенда отключается (`.enabled` → `.enabled.local-off`)

## Деплой на prod

**Git после правок — всегда.** **Prod — только по явной просьбе.**

| | |
|---|---|
| SSH / host | `45.90.35.63` |
| Path | `/var/www/lorshop.ru/data/www/lorshop.ru` |
| Remote | https://github.com/bziksv/lorshop |

```bash
cd lorshop.ru
git add … && git commit -m "…" && git push origin main

# только когда пользователь просит выкатить на сервер
```

**Запрещено:** автодеплой на prod, правки на prod без commit, `scp` файлов кода.

## Проверка

```bash
curl -sS -o /dev/null -w '%{http_code}\n' https://lorshop.ru/
curl -sS -o /dev/null -w '%{http_code}\n' http://127.0.0.1:8098/
curl -sS -o /dev/null -w '%{http_code}\n' http://127.0.0.1:8098/catalog/
```

## Совместимость PHP 8.3

В `index.php` (блок популярных категорий) ключи массива были без кавычек (`$arSection[ID]`) — на PHP 8 это fatal. Для локалки исправлено на `$arSection['ID']` и т.п.