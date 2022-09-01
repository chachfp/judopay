<?php

/*

This PHP receives a form post from the index.html file
with the following : cardholdername, card number, cvv and expiry date
There is no error checking in this code as it is for example only
The only check is if there are any post variables. If none it will just return
with Nothing To See Here!. You should pre-process the form within the index.html
and again should santize the data received here also

*/


if (!empty($_POST)) {

// Include JudoPay PHP SDK

require("vendor/autoload.php");

// Creates a Random ID ( used for consumer reference and payment reference )
// You should / can use this or your own cosumer references and payment references
// if required

function guidv4()
    {
        if (function_exists('com_create_guid') === true)
            return trim(com_create_guid(), '{}');

        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

// Setup Judo Object with JudoID + APIToken / Secrets

include("./JudoCredentials.php");

// Setup Payment Object, this uses a pre-auth model
// For a payment model use CardPayment

$payment = $judopay->getModel('CardPreauth');    
   $attributes = [

        // Example Amount of 1.00, this would be your
        // transaction value you require
        'amount' => 1.00,
        // Card Number from $_POST variable from index.html
        'cardNumber' => $_POST['cardNumber'],
        // Expiry Data from $_POST variable from index.html
        'expiryDate' => $_POST['cardExpiryDate'],
        // Consumer Reference from guid4() function
        'yourConsumerReference' => guidv4(),
        // Payment Reference from quid4() function
        'yourPaymentReference' => guidv4(),
        // CVV from $_POST variable from index.html
        'cv2' => $_POST['CVV'],
        // Currency
        'currency' => 'GBP',
        // JudoID from JudoCredentials.php
        'judoId' => $judoId,
        // Card Holder Name from $_POST varaible from index.html
        'cardHolderName' => $_POST['cardHolderName'],
        // Mobile number obtained from JudoCredentials.php, you would
        // set this from your consumer records
        'mobileNumber' => $mobileNumber,
        // Phone Country Code from JudoCredentials.php, you would
        // set this from your consumer records
        'phoneCountryCode' => $phoneCountryCode,
        // Email Address from JudoCredentials.php, you would
        // set this from your consumer records  
        'emailAddress' => $emailAddress,
        'threeDSecure' => [
            // Default 3ds2 Secure Details
            'authenticationSource' => 'BROWSER',
            'methodCompletion' => 'no',
            // Always Mandata a Challenge
            //'challengeRequestIndicator' => 'challengeAsMandate',
    ]       
    ];   $payment->setAttributeValues($attributes);    

// Try and submit the payment request and echo the answer
// This will return the result back to the calling javascript in 
// index.html

try
    {
        // Send the payment request to Judo for processing

        $payment->setAttributeValues($attributes);
        $response = $payment->create();
        echo json_encode($response);  
    
    // Encode any error or exception into a json message
    // and return result back to the calling javascript in index.html to handle

    } catch (JudopayExceptionValidationError $e) {
        echo ("{\"Error\":\"".$e->getSummary()."\",\"result\":\"Error\"}");
    } catch (JudopayExceptionApiException $e) {
        echo ("{\"Error\":\"".$e->getSummary()."\",\"result\":\"Error\"}");
    } catch (Exception $e) {
        echo ("{\"Error\":\"".$e->getMessage()."\",\"result\":\"Error\"}");
    }

} else {

    // Default Output if no $_POST received

    echo "{\"result\":\"Nothing To See Here!\"}";
}

?>
