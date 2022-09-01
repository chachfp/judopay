<?php

// Credentials for PHP APITokens / JudoID / Secret to use for requests
$judoId = "";
$apiToken = "";
$apiSecret = "";

// Set production = true to use the production environment
$production = false;

// Default Values for Initial Request - This would be variables in a real world
// Environment, and would not be in here, but for sample this is included here
// to parse data into the postpayment.php

$mobileNumber = "07999999999";
$phoneCountryCode = "44";
$emailAddress = "contact@judopay.com";

// Setup the JudoPay Main Object
    $judopay = new Judopay(
        array(
            'apiToken' => $apiToken,
            'apiSecret' => $apiSecret,
            'judoId' => $judoId, 
            'useProduction' => $production
        )
    );

?>
