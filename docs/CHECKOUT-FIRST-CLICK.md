# Оформление заказа: только со 2-го клика (PHP 8.2)

**Симптом:** лоадер мигает и гаснет, заказ уходит со **второго** клика.  
**Эталон без бага:** [kawe.su](https://kawe.su/), [gnkmed.ru](https://gnkmed.ru/).

## Корневая причина

В `b_module_to_module` висит мёртвый обработчик:

`OnUserLogin` → `CGroupAuthProvider::OnUserLogin`

Метода **нет**. На PHP 8.2 при `Authorize` (гость / регистрация при оформлении) → **TypeError 500**.  
Сессия уже успевает создаться → второй клик проходит без `Authorize`.

На kawe этой строки в БД нет.

## Проверка (1 минута)

```sql
SELECT ID, MESSAGE_ID, TO_CLASS, TO_METHOD
FROM b_module_to_module
WHERE MESSAGE_ID = 'OnUserLogin' AND TO_CLASS = 'CGroupAuthProvider';
```

В Network на первом клике: `…/sale.order.ajax/ajax.php` → **500**, в теле:

`CGroupAuthProvider does not have a method "OnUserLogin"`

## Фикс

```sql
DELETE FROM b_module_to_module
WHERE MESSAGE_ID = 'OnUserLogin'
  AND TO_CLASS = 'CGroupAuthProvider'
  AND TO_METHOD = 'OnUserLogin';
```

Сброс кеша событий:

```bash
rm -rf bitrix/managed_cache/*
chown -R <site_user>:<site_group> <site_root>
```

Чтобы не вернулось после обновления ядра — в `bitrix/php_interface/init.php`:

```php
if (function_exists('UnRegisterModuleDependences')) {
	UnRegisterModuleDependences('main', 'OnUserLogin', 'main', 'CGroupAuthProvider', 'OnUserLogin');
}
```

## Не путать с

Глобальный jQuery `$(document).on('submit', 'form', …)` в `functions.js` — отдельно: лучше **не** вешать на все формы (как на kawe), либо явно пропускать `#bx-soa-order-form`. Это не замена фиксу события выше.
