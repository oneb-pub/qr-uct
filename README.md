# Requirements
- php >= 7.4
- globalcitizen/php-iban
- webmozart/assert
# Install
```bash
composer require plakidan/qr-uct
```
# Usage
```php
$generator = new \UCT\Generator();

$generator->setAmount(10.1)
    ->setCurrency('UAH')
    ->setPaymentPurpose('Оплата згідно інвойсу #1B-22052024/2')
    ->setReceiverAccount('UA663006140000026007500287926')
    ->setReceiverCode('33051277')
    ->setReceiverName('ТОВ  "КСИКОМ КОНСАЛТІНГ"');

$url = $generator->generateUrl();
```