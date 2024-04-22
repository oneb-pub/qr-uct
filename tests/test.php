<?php

include_once __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . '/../src/UCT/Generator.php';

$generator = new \UCT\Generator();
$generator
    ->setAmount(59.14)
    ->setCurrency('UAH')
    ->setPaymentPurpose('Благодійний безповоротний внесок')
    ->setReceiverAccount('UA473052990000026005026707459')
    ->setReceiverCode('43720363')
    ->setReceiverName('БО "Фонд Сергія Притули"');

//url
$url = $generator->generateUrl();

//qr svg
$qr = $generator->generateQrCodeSvg();

die($qr.PHP_EOL);