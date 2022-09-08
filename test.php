<?php

use GuzzleHttp\Client;

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
var_dump('Transaction timestamp: ' . $timestamp);
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

// handle response
if ($response['result'] == 'Additional device data is needed for 3D Secure 2') {
  // resume 3DS2
  $resumeRequest = $judopay->getModel('ResumeThreeDSecureTwo');
  $attributes = [
      'receiptId' => $response['receiptId'],
      'cv2' => '452',
      'methodCompletion' => 'no'
  ];
}

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
if ($response['result'] == 'Challenge completion is needed for 3D Secure 2') {
  // check challenge
  var_dump("Submitting form to 3ds2 provider to render challenge");
  $client = new Client();
  try {
    $guzzleResponse = $client->post($response['challengeUrl'], [
        'creq' => $response['cReq']
    ]);
  } catch (\Exception $exception) {
    var_dump('Guzzle exception, message: ' . $exception->getMessage());
  }
  echo $guzzleResponse->getStatusCode();
// "200"
  echo $guzzleResponse->getHeader('content-type')[0];
// 'application/json; charset=utf8'
  echo $guzzleResponse->getBody();
  var_dump($guzzleResponse->getBody());
  // 3rd part
  $completeRequest = $judopay->getModel('CompleteThreeDSecureTwo');
  $attributes = [
      'receiptId' => $response['receiptId'],
      'cv2' => '452'
  ];
}

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