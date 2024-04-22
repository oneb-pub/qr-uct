<?php

namespace UCT;
use Webmozart\Assert\Assert;
use PHP_IBAN\IBAN;
use splitbrain\phpQRCode\QRCode;

class Generator
{
    const ENCODING_WIN1251 = '2';
    const ENCODING_UTF8 = '1';

    private $national_bank_qr_endpoint = 'https://bank.gov.ua/qr';

    /** @var string $service_mark Службова мітка */
    private $service_mark = 'BCD';

    /** @var string $format_version Версія формату */
    private $format_version = '002';

    /** @var string $encoding Кодування для полів позначених зірочкою у додатк до постанови. 2 - Win1251. 1 - UTF-8 */
    private $encoding = '1';

    /** @var string $function Функція визначається її ключовими значеннями: кредитовий переказ – Ukrainian Credit Transfer */
    private $function = 'UCT';

    /** @var string $bank_identifier_code Для версії формату 002 поле “BIC” зарезервовано */
    private $bank_identifier_code = '';

    /** @var string $receiver Містить прізвище, ім’я, по батькові фізичної особи або найменування юридичної особи */
    private $receiver = '';

    /** @var string $account Номер рахунку отримувача */
    private $account = '';

    /** @var string $currency Валюта платежу з трьох букв, ISO4217 */
    private $currency = '';

    /** @var string $amount Сума платежу */
    private $amount = '1';

    /** @var string $receiver_code РНОКПП або Серія та номер паспорту або код ЄДРПОУ отримувача */
    private $receiver_code = '';

    /** @var string $target_code Зарезервовано для подальшого використання */
    private $target_code = '';

    /** @var string $reference Зарезервовано для подальшого використання */
    private $reference = '';

    /** @var string $payment_purpose Містить інформацію про платіж у текстовій формі */
    private $payment_purpose = '';

    /**
     * @var string $display_text
     * Містить текст, призначений для
     * виведення на дисплей або друку. Цей текст не включається до даних операції
     * переказу коштів і має бути показаний користувачеві після розкодування QR-коду.
     * Крім того, цей текст у незмінному або зміненому вигляді може
     * використовуватись у системах обробки даних для деталізації даних операції
     */
    private $display_text = '';

    /**
     * @param Generator::ENCODING_WIN1251|Generator::ENCODING_UTF8 $encoding
     * @return self
     * Усі текстові дані мають бути надані у вказаному кодуванні
     */
    public function setEncoding(string $encoding): self
    {
        Assert::oneOf($encoding,[self::ENCODING_WIN1251,self::ENCODING_UTF8],'Encoding const expected, but got %s');

        $this->encoding = $encoding;
        
        return $this;
    }

    /**
     * Містить прізвище, ім’я, по батькові фізичної
     * особи або найменування юридичної особи. Довжина значення
     * елемента не повинна перевищувати довжину 38 символів
     * @param string $receiver
     * @return $this
     */
    public function setReceiverName(string $receiver): self
    {
        Assert::lengthBetween($receiver,1,70);
        $this->checkEncoding($receiver);

        $this->receiver = $receiver;

        return $this;
    }

    /**
     * Містить номер рахунку отримувача.
     * Довжина значення елемента не повинна перевищувати
     * довжину елемента 29 символів
     * @param string $iban
     * @return $this
     */
    public function setReceiverAccount(string $iban): self
    {
        Assert::maxLength($iban,29);
        $validator = new IBAN();
        if(!$validator->Verify($iban)){
            throw new \InvalidArgumentException('Invalid IBAN');
        }

        $this->account = $iban;

        return $this;
    }

    /**
     * Валюта країни з трьох букв у верхньому регистрі згідно ISO 4217
     * @param string $currency
     * @return $this
     */
    public function setCurrency(string $currency): self
    {
        $this->checkEncoding($currency);
        $currency = mb_strtoupper($currency);
        Assert::length($currency,3);

        $this->currency = $currency;

        return $this;
    }

    /**
     * Максимальне число становить 999999999.99
     * Якщо сума містить дрібну частину одиниці валюти, то ця дрібна
     * частина обов’язково складається з двох цифрових символів
     * @param $amount
     * @return $this
     */
    public function setAmount($amount): self
    {
        Assert::numeric($amount);
        Assert::lessThanEq($amount, 999999999.99);
        $scaled = $amount * 100;
        Assert::same((float)intval($scaled), (float)$scaled, "Not more 2 digits after comma");
        if ((float)$amount == (int)$amount) {
            // Якщо так, конвертуємо число у ціле
            $amount = number_format($amount);
        }else{
            $amount = number_format($amount,2,'.','');
        }

        $this->amount = $amount;

        return $this;
    }

    /**
     * РНОКПП або серію (за наявності) та номер паспорта отримувача адо код ЄДРПОУ
     * @param string $code
     * @return $this
     */
    public function setReceiverCode(string $code): self
    {
        //For Ukraine
        Assert::regex($code,'/^(?:\d{8}|\d{10}|[\p{L}]{2}\d{6})$/u');
        $code = mb_strtoupper($code);
        $this->receiver_code = $code;

        return $this;
    }

    /**
     * Призначення платежу
     * @param string $purpose
     * @return $this
     */
    public function setPaymentPurpose(string $purpose): self
    {


        $this->payment_purpose = $purpose;

        return $this;
    }

    /**
     * ПРАЦЮЄ НЕ ВСЮДИ
     * Містить текст, призначений для
     * виведення на дисплей або друку. Цей текст не включається до даних операції
     * переказу коштів і має бути показаний користувачеві після розкодування QR-коду.
     * Крім того, цей текст у незмінному або зміненому вигляді може
     * використовуватись у системах обробки даних для деталізації даних операції
     * @param string $text
     * @return $this
     */
    public function setDisplayText(string $text): self
    {
        Assert::maxLength($text,70);
        $this->checkEncoding($text);

        $this->display_text = $text;

        return $this;
    }

    /**
     * Генерація посилання
     * @return string
     */
    public function generateUrl(): string
    {

        $string = $this->service_mark.PHP_EOL;
        $string.= $this->format_version.PHP_EOL;
        $string.= $this->encoding.PHP_EOL;
        $string.= $this->function.PHP_EOL;
        $string.= $this->bank_identifier_code.PHP_EOL;
        Assert::lengthBetween($this->receiver,1,70,'Receiver length must be between 1 and 70, got %s');
        $string.= $this->receiver.PHP_EOL;
        $string.= $this->account.PHP_EOL;
        $string.= $this->currency.$this->amount.PHP_EOL;
        Assert::lengthBetween($this->receiver_code,8,10,'Receiver code must be 8 or 10 symbols, got %s');
        $string.= $this->receiver_code.PHP_EOL;
        $string.= $this->target_code.PHP_EOL;
        $string.= $this->reference.PHP_EOL;
        Assert::lengthBetween($this->payment_purpose,10,140,'Payment purpose must be between 10 and 140 symbols, got %s');
        $string.= $this->payment_purpose.PHP_EOL;
        Assert::maxLength($this->display_text,70,'Display text must be between maximum 70 symbols, got %s');
        $string.= $this->display_text.PHP_EOL;

        $encoded = trim(base64_encode($string),'=');
        $encoded = str_replace('/','_',$encoded);
        $encoded = str_replace('+','-',$encoded);
        return $this->national_bank_qr_endpoint.'/'.$encoded;
    }

    /**
     * Генерація QR коду у форматі SVG
     * @return string
     */
    public function generateQrCodeSvg(): string
    {
        return QRCode::svg($this->generateUrl());
    }

    private function checkEncoding(string $string): void
    {
        $encoding = mb_detect_encoding($string);

        if($this->encoding == self::ENCODING_UTF8){
            Assert::true(strtolower($encoding) === 'utf-8' || strtolower($encoding) == 'ascii', "Expected encoding: UTF-8. Got encoding: " . mb_detect_encoding($encoding));
        }elseif ($this->encoding == self::ENCODING_WIN1251){
            Assert::true(strtolower($encoding) === 'windows-1251', "Expected encoding: UTF-8. Got encoding: " . mb_detect_encoding($encoding));
        }else{
            Assert::oneOf($this->encoding,[self::ENCODING_WIN1251,self::ENCODING_UTF8],'Encoding const expected, but got %s');
        }
    }
}