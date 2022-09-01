<?php

/*

This PHP receives a form post from the index.html file
with the following : receiptID, CVV,methodCompletion
in order to continue the 3ds2 processing journey
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

// Setup Resume Object with form fields received from index.html

 $resumeRequest = $judopay->getModel('ResumeThreeDSecureTwo');
        $attributes = [
            'receiptId' => $_POST['receiptID'],
            'cv2' => $_POST['CVV'],
            'methodCompletion' => $_POST['methodCompletion']
        ];

// Send the Resume request to Judo to resume the 3ds2 journey
try
    {

        // If Succeeds return result to index.html

        $resumeRequest->setAttributeValues($attributes);  
        $response = $resumeRequest->update();
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
