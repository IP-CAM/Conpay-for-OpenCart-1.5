Модули Conpay.ru для OpenCart
=============================

## Установка модуля

1. Скопируйте файлы модуля в следующие папки CMS:
* `/admin/controller/module/conpay.php`
* `/admin/language/russian/module/conpay.php`
* `/admin/view/template/module/conpay.tpl`
* `/catalog/controller/module/conpay.php`
* `/catalog/language/russian/module/conpay.php`
* `/catalog/view/theme/[default]/template/module/conpay.tpl`
2. В меню "Расширения -> Модули" установите модуль CONPAY.RU.
3. Внесите настройки модуля и добавьте модуль в шаблоны товара и каталога ("Добавить модуль"). Убедитесь, что модуль активирован. Сохраните настройки.
4. Добавить HTML-контейнер кнопки в шаблоны:
*   в шаблоне товара `/catalog/view/theme/[default]/template/product/product.tpl` добавить контейнер кнопки:
```php
<div id="[ID контейнера кнопки в настройках модуля]-<?php echo $product_id; ?>"></div>
```
*   в шаблоне товара `/catalog/view/theme/[default]/template/product/category.tpl` добавить контейнер кнопки в цикле перечисления товаров:
```php
<div id="[ID контейнера кнопки в настройках модуля]-<?php echo $product_id; ?>"></div>
```

## Установка модуля платежной системы

1. Скопируйте файлы модуля в следующие папки CMS:
* `/admin/controller/payment/conpay_payment_system.php`
* `/admin/language/russian/payment/conpay_payment_system.php`
* `/admin/view/template/payment/conpay_payment_system.tpl`
* `/catalog/controller/payment/conpay_payment_system.php`
* `/catalog/language/russian/payment/conpay_payment_system.php`
* `/catalog/model/payment/conpay_payment_system.php`
* `/catalog/view/theme/[default]/template/payment/conpay_payment_system.tpl`
2. В меню "Расширения -> Платежные системы" установите модуль платежной системы CONPAY.RU.
3. Внесите настройки и активируйте модуль. Сохраните настройки.
