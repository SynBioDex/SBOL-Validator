<?php

require_once("ValidationRequest.php");
require_once("APIRequest.php");

$json = "";

if(isset($HTTP_RAW_POST_DATA)) {
    $json = json_decode($HTTP_RAW_POST_DATA, true);
} else {
    err("No POST data received!");
}

checkIncorrect($json);

if($json == null || $json === false) {
    err("Incorrectly formatted JSON");
} else {
    $request = new APIRequest($json);

    //var_dump($request);
    //echo "{\"command\": \"" . $request->generateCommand() . "\", \"result\": \"". base64_encode($request->executeValidation()) ."\"}";
    echo "{\"result\": \"". $request->executeValidation() ."\"}";

}


function err($reason) {
    echo $reason;
    http_response_code(400);
    die(400);
}

function checkIncorrect($json)
{
    if(!isset($json["wantFileBack"])) {
        err("wantFileBack");
    }

    if(!isset($json["validationOptions"]["sbol11To20"])) {
        err("sbol11To20");
    }

    if(!isset($json["validationOptions"]["sbol20ToGenBank"])) {
        err("sbol20ToGenBank");
    }

    if(!isset($json["validationOptions"]["genBankToSbol20"])) {
        err("genBankToSbol20");
    }

    if(!isset($json["validationOptions"]["diff"])) {
        err("diff");
    }

    if(!isset($json["validationOptions"]["noncompliantUrisAllowed"])) {
        err("noncompliantUrisAllowed");
    }

    if(!isset($json["validationOptions"]["incompleteDocumentsAllowed"])) {
        err("incompleteDocumentsAllowed");
    }

    if(!isset($json["validationOptions"]["bestPracticesCheck"])) {
        err("bestPracticesCheck");
    }

    if(!isset($json["validationOptions"]["failOnFirstError"])) {
        err("failOnFirstError");
    }

    if(!isset($json["validationOptions"]["displayFullErrorStackTrace"])) {
        err("displayFullErrorStackTrace");
    }

    if(!isset($json["validationOptions"]["ComponentDefinitionUri"])) {
        err("ComponentDefinitionUri");
    }

    if(!isset($json["validationOptions"]["uriPrefix"])) {
        err("uriPrefix");
    }

    if(!isset($json["validationOptions"]["version"])) {
        err("version");
    }

    if(!isset($json["mainFile"])) {
        err("");
    }

    if($json["validationOptions"]["diff"] != isset($json["diffFile"])) {
        err("diffFile");
    }
}
    ?>