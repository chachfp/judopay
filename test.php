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
    'cardNumber' => '4976000000003436',
    'expiryDate' => '12/25',
    'yourConsumerReference' => rand(0, 1000000000000),
    'yourPaymentReference' => rand(0, 1000000000000),
    'cv2' => '452',
    'cardHolderName' => 'Jimi Hendrix',
    'currency' => 'GBP',
    'judoId' => '100052109',
    'threeDSecure' => [
        'authenticationSource' => 'BROWSER',
        'methodCompletion' => 'no',
    ]
];
$timestamp = $date = date('Y-m-d H:i:s');
var_dump($timestamp);
$response = null;

try {
  $payment->setAttributeValues($attributes);
  $response = $payment->create();
  var_dump($response);
} catch (JudopayExceptionValidationError $e) {
  echo("{\"Error\":\"" . $e->getSummary() . "\",\"result\":\"Error\"}");
} catch (JudopayExceptionApiException $e) {
  echo("{\"Error\":\"" . $e->getSummary() . "\",\"result\":\"Error\"}");
} catch (Exception $e) {
  echo("{\"Error\":\"" . $e->getMessage() . "\",\"result\":\"Error\"}");
}

// 2nd part
$resumeRequest = $judopay->getModel('ResumeThreeDSecureTwo');
$attributes = [
    'receiptId' => $response['receiptId'],
    'cv2' => '452',
    'methodCompletion' => 'no'
];

try {
  $resumeRequest->setAttributeValues($attributes);
  $response = $resumeRequest->update();
  var_dump('2nd part');
  var_dump($response);
} catch (JudopayExceptionValidationError $e) {
  echo("{\"Error\":\"" . $e->getSummary() . "\",\"result\":\"Error\"}");
} catch (JudopayExceptionApiException $e) {
  echo("{\"Error\":\"" . $e->getSummary() . "\",\"result\":\"Error\"}");
} catch (Exception $e) {
  echo("{\"Error\":\"" . $e->getMessage() . "\",\"result\":\"Error\"}");
}

// 3rd part
$completeRequest = $judopay->getModel('CompleteThreeDSecureTwo');
$attributes = [
    'receiptId' => $response['receiptId'],
    'cv2' => '452'
];

// Submit the object to Judo to complete the 3ds2 journey
try {
  $completeRequest->setAttributeValues($attributes);
  $response = $completeRequest->update();
  var_dump('3rd part');
  var_dump($response);
} catch (JudopayExceptionValidationError $e) {
  echo("{\"Error\":\"" . $e->getSummary() . "\",\"result\":\"Error\"}");
} catch (JudopayExceptionApiException $e) {
  echo("{\"Error\":\"" . $e->getSummary() . "\",\"result\":\"Error\"}");
} catch (Exception $e) {
  echo("{\"Error\":\"" . $e->getMessage() . "\",\"result\":\"Error\"}");
}