<?php

/*

This PHP receives a form post from the index.html file
with the following : receiptID, CVV

There is no error checking in this code as it is for example only
The only check is if there are any post variables. If none it will just return
with Nothing To See Here!. You should pre-process the form within the index.html
and again should santize the data received here also

*/


// If No Post Variables will quit Quit
if (!empty($_POST)) {

// Include Judo SDK
require("vendor/autoload.php");

// Setup Judo Object with JudoID + APIToken / Secrets

include("./JudoCredentials.php");

// Setup the complete object with values received from index.html

   $completeRequest = $judopay->getModel('CompleteThreeDSecureTwo');
        $attributes = [
            'receiptId' => $_POST['receiptID'],
            'cv2' => $_POST['CVV']
        ];

// Submit the object to Judo to complete the 3ds2 journey
try
    {
        // If succeeds send the result back to index.html

        $completeRequest->setAttributeValues($attributes);  
        $response = $completeRequest->update();
        echo json_encode($response);  

    // Any Error encode in JSON and send back to index.html

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