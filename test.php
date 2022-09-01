<?php
require("vendor/autoload.php");

$judopay = new \Judopay(
    array(
        'apiToken' => 'l4iq3SND2nU5U8rM',
        'apiSecret' => '7d18a83c6e647a79fff18c09b8cac123289100420491ee77f2a99a298e9bf603',
        'judoId' => '100052109',
        'useProduction' => false,
    )
);

$payment = $judopay->getModel('CardPreauth');

$attributes = [
    'amount' => 2.00,
    'cardNumber' => "4976000000003436",
    'expiryDate' => "12/25",
    'yourConsumerReference' => rand(0, 1000000000000),
    'yourPaymentReference' => rand(0, 1000000000000),
    'cv2' => "452",
    'currency' => 'GBP',
    'judoId' => '100052109',
    'threeDSecure' => [
        'authenticationSource' => 'BROWSER',
        'methodCompletion' => 'no',
    ]
];

try {
  $payment->setAttributeValues($attributes);
  $response = $payment->create();
  var_dump($response);
} catch (\Exception $e) {
  echo $e->getMessage();
  return;
}