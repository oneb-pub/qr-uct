# Requirements
- php >= 7.4
- globalcitizen/php-iban
- webmozart/assert
# Install
```bash
composer require oneb/qr-uct
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
# Banks
- ![Sense Bank](https://bank.gov.ua/admin_uploads/article/SenseBank_qr_page_logo.jpg)
- ![Грант](https://bank.gov.ua/admin_uploads/article/GrantBank_qr_page_logo.jpg)
- ![Укргазбанк](https://bank.gov.ua/admin_uploads/article/Ukrgazbank_qr_page_logo.jpg)
- ![ПриватБанк](https://bank.gov.ua/admin_uploads/article/Privat_qr_page_logo.png)
- ![Monobank | Universal Bank](https://bank.gov.ua/admin_uploads/article/Mono_qr_page_logo.png)
- ![izibank](https://bank.gov.ua/admin_uploads/article/Izibank_qr_page_logo.png)
- ![Sportbank](https://bank.gov.ua/admin_uploads/article/sportbank_qr_page_logo.png)
- ![Банк Кредит Дніпро](https://bank.gov.ua/admin_uploads/article/KD_qr_page_logo.jpg)
- ![ПУМБ](https://bank.gov.ua/admin_uploads/article/pumb_qr_page_logo.png)
- ![Aбанк](https://bank.gov.ua/admin_uploads/article/A_bank_qr_page_logo.png)
- ![Глобус Банк](https://bank.gov.ua/admin_uploads/article/Globusbank_qr_page_logo.jpg)
- ![РАДАБАНК](https://bank.gov.ua/admin_uploads/article/Radabank_qr_page_logo.jpg)
- ![Креді Агріколь Банк](https://bank.gov.ua/admin_uploads/article/Credit_agricole_qr_page_logo.jpg)
- ![GERC](https://bank.gov.ua/admin_uploads/article/Gerc_qr_page_logo.jpg)
- ![Komunalka](https://bank.gov.ua/admin_uploads/article/Komunalka_qr_page_logo.png)