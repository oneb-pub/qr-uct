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

$generator
    ->setAmount(10.1) //Сумма у гривнях
    ->setCurrency('UAH') //Валюта
    ->setPaymentPurpose('Благодійний безповоротний внесок') //Призначення платежу
    ->setReceiverAccount('UA473052990000026005026707459') //Рахунок IBAN
    ->setReceiverCode('43720363') // РНОКПП або ЄДРПОУ
    ->setReceiverName('БО "Фонд Сергія Притули"'); // ПІБ або на назва юридичної особи отримувача

$url = $generator->generateUrl();
```