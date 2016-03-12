<?php

require_once("PHPTAL-1.3.0/PHPTAL.php");

$template = new PHPTAL("form.html");
$template->setOutputMode(PHPTAL::HTML5); 	

try {	 
    echo $template->execute();
}
catch (Exception $e){
    echo $e;
}

?>