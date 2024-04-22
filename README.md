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
    ->setAmount(10.1)
    ->setCurrency('UAH')
    ->setPaymentPurpose('Благодійний безповоротний внесок')
    ->setReceiverAccount('UA473052990000026005026707459')
    ->setReceiverCode('43720363')
    ->setReceiverName('БО "Фонд Сергія Притули"');

$url = $generator->generateUrl();
```