<?php

session_start();

require_once("PHPTAL-1.3.0/PHPTAL.php");
require_once("ValidationRequest.php");

$template = new PHPTAL("result.html");

$template->request = unserialize($_SESSION["request"]);
$template->setOutputMode(PHPTAL::HTML5); 

try {	 
    echo $template->execute();
}
catch (Exception $e){
    echo $e;
}

?>