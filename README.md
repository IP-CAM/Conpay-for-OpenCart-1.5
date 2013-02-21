������ Conpay.ru ��� OpenCart
=============================

## ��������� ������

1. ���������� ����� ������ � ��������� ����� CMS:
* `/admin/controller/module/conpay.php`
* `/admin/language/russian/module/conpay.php`
* `/admin/view/template/module/conpay.tpl`
* `/catalog/controller/module/conpay.php`
* `/catalog/language/russian/module/conpay.php`
* `/catalog/view/theme/[default]/template/module/conpay.tpl`
2. � ���� "���������� -> ������" ���������� ������ CONPAY.RU.
3. ������� ��������� ������ � �������� ������ � ������� ������ � �������� ("�������� ������"). ���������, ��� ������ �����������. ��������� ���������.
4. �������� HTML-��������� ������ � �������:
* � ������� ������ `/catalog/view/theme/[default]/template/product/product.tpl` �������� ��������� ������:
```php
<div id="[ID ���������� ������ � ���������� ������]-<?php echo $product_id; ?>"></div>
```
* � ������� ������ `/catalog/view/theme/[default]/template/product/category.tpl` �������� ��������� ������ � ����� ������������ �������:
```php
<div id="[ID ���������� ������ � ���������� ������]-<?php echo $product_id; ?>"></div>
```

## ��������� ������ ��������� �������

1. ���������� ����� ������ � ��������� ����� CMS:
* `/admin/controller/payment/conpay_payment_system.php`
* `/admin/language/russian/payment/conpay_payment_system.php`
* `/admin/view/template/payment/conpay_payment_system.tpl`
* `/catalog/controller/payment/conpay_payment_system.php`
* `/catalog/language/russian/payment/conpay_payment_system.php`
* `/catalog/model/payment/conpay_payment_system.php`
* `/catalog/view/theme/[default]/template/payment/conpay_payment_system.tpl`
2. � ���� "���������� -> ��������� �������" ���������� ������ ��������� ������� CONPAY.RU.
3. ������� ��������� � ����������� ������. ��������� ���������.
