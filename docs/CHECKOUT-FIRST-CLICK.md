# Оформление заказа: клик «Оформить заказ» с первого раза

Проблема: лоадер мигает, заказ уходит только со **второго** клика.  
На эталонах ([kawe.su](https://kawe.su/), [gnkmed.ru](https://gnkmed.ru/)) такого нет.

## Причины (две, часто вместе)

1. **Глобальный jQuery-перехват `submit` всех форм** в `js/functions.js`  
   Bitrix сохраняет заказ так: `BX.ajax.submit` → `BX.submit` → клик по скрытой кнопке `type=submit` → событие `submit` у формы.  
   Глобальный обработчик согласия (для подписки и т.п.) может сорвать **первый** submit у `#bx-soa-order-form`.  
   На kawe/gnkmed такого глобального `$(document).on('submit', 'form', …)` **нет**.

2. **Нет `BX.ajax.submitAjax` на странице**  
   Метод лежит в `/bitrix/js/main/core/core_ajax.js`. В `kernel_main` его часто нет как отдельного скрипта.  
   Старый iframe-`submit` на PHP 8.x даёт гонки/битый JSON → лоадер гаснет без редиректа.

## Что сделать на другом проекте (medical-templates)

### 1. `bitrix/templates/medical-templates/js/functions.js`

Найти глобальный перехват и **исключить форму заказа**:

```js
$(document).on('submit', 'form', function (e) {
    var $form = $(this);
    if ($form.closest('#callback').length) return;
    // обязательно:
    if ($form.is('#bx-soa-order-form') || $form.closest('#bx-soa-order-form').length) return;
    if (!consentOk($form)) {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    }
});
```

Если такого глобального `submit` на `form` нет (как на kawe) — шаг пропускается.

### 2. `…/sale.order.ajax/order.ajax/template.php`

Перед `order_ajax.js` подключить:

```php
$this->addExternalJs('/bitrix/js/main/core/core_ajax.js');
$this->addExternalJs($templateFolder.'/order_ajax.js');
```

### 3. `…/sale.order.ajax/order.ajax/order_ajax.js`

**A. В `clickOrderSaveAction`** — как на gnkmed, сброс лоадера до сохранения:

```js
clickOrderSaveAction: function(event)
{
    this.disallowOrderSave();
    this.endLoader();

    if (!this.isCustomConsentChecked()) {
        this.showCustomConsentError();
        return BX.PreventDefault(event);
    }
    this.hideCustomConsentError();

    if (this.isValidForm()) {
        this.allowOrderSave();
        this.doSaveAction();
    }
    return BX.PreventDefault(event);
},
```

**B. В `sendRequest` для `saveOrderAjax`** — `submitAjax` с fallback:

```js
if (action === 'saveOrderAjax')
{
    form = BX('bx-soa-order-form');
    if (form) {
        form.querySelector('input[type=hidden][name=sessid]').value = BX.bitrix_sessid();
    }

    if (BX.type.isFunction(BX.ajax.submitAjax))
    {
        var saveData = {
            via_ajax: 'Y',
            sessid: BX.bitrix_sessid(),
            SITE_ID: this.siteId,
            signedParamsString: this.signedParamsString
        };
        saveData[this.params.ACTION_VARIABLE || 'soa-action'] = 'saveOrderAjax';

        BX.ajax.submitAjax(form, {
            url: this.ajaxUrl,
            method: 'POST',
            dataType: 'json',
            data: saveData,
            onsuccess: BX.proxy(this.saveOrderWithJson, this),
            onfailure: BX.proxy(this.handleNotRedirected, this)
        });
    }
    else
    {
        BX.ajax.submit(form, BX.proxy(this.saveOrder, this));
    }
}
```

**C.** Добавить `saveOrderWithJson` + `handleNotRedirected` (как в стоковом Bitrix `.default`),  
а старый `saveOrder` оставить для fallback (парсинг HTML/JSON из iframe, срезать PHP Notice перед `{`).

Эталон в репо lorshop: коммит `f112294`  
(`order_ajax.js`, `template.php`, `functions.js`).

## После правок

```bash
# локально
git add … && git commit -m "…" && git push origin main

# prod
ssh <host> 'cd <site_root> && git pull --ff-only origin main'
# сброс кеша Bitrix
rm -rf bitrix/cache/js/* bitrix/cache/css/* bitrix/managed_cache/*
chown -R <site_user>:<site_group> <site_root>
```

В браузере: **Ctrl+F5** на `/personal/order/make/`, один клик «Оформить заказ».

## Быстрая проверка «это та же болезнь»

| Проверка | Плохо | Ок (как kawe/gnkmed) |
|----------|--------|----------------------|
| `functions.js`: `on('submit', 'form'` | есть, без исключения `#bx-soa-order-form` | нет или есть exclude |
| В HTML заказа есть `core_ajax.js` / `core_ajax.min.js` как `<script src>` | только в `BX.setJSList`, без src | есть `<script src="…core_ajax…">` |
| В консоли `typeof BX.ajax.submitAjax` | `"undefined"` | `"function"` |
