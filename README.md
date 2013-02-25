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
2. В меню "Дополнения -> Модули" установите модуль CONPAY.RU.
3. Внесите слудующие настройки модуля:
    * ID-продавца
    * API-ключ
    * Ключ ответа
    * Минимальная сумма кредита: минимальная сумма заказа, оплачиваемого в кредит. По умолчанию - 3000 руб.
4. Добавьте модуль в шаблоны товара и каталога (кнопка "Добавить модуль" в настройках модуля):
    * Схема: Product - для страницы товара, Category - для категории товаров
    * Расположение: Верх страницы
    * Статус: Включено
5. Сохраните настройки.
6. Добавить HTML-контейнер кнопки в шаблоны (соответствующие файлы шаблонов приложены к модулю в качестве образца расположения контейнера кнопки:
    * в шаблоне товара `/catalog/view/theme/[default]/template/product/product.tpl` добавить контейнер кнопки:
    ```php
    <div id="[ID контейнера кнопки в настройках модуля]-<?php echo $product_id; ?>"></div>
    ```
    * в шаблоне товара `/catalog/view/theme/[default]/template/product/category.tpl` добавить контейнер кнопки в цикле перечисления товаров:
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
2. В меню "Дополнения -> Оплата" установите модуль платежной системы CONPAY.RU.
3. Внесите следующие настройки:
    * Нижняя граница: минимальная сумма заказа, оплачиваемого в кредит. По умолчанию - 3000 руб.
    * HTML-содержание кнопки: содержание кнопки, выводимой после завершения оформления заказа.
    * Статус заказа после оплаты: Ожидание.
    * Статус: Включено.
4. Сохраните настройки.
