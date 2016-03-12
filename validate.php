
<?php

require_once("ValidationRequest.php");

session_start();

$request = new ValidationRequest();

if($_POST["usePaste"] == "true" ) {
	$target_dir = "uploads/";
	$target_file = $target_dir . basename($_FILES["primaryInputFile"]["name"]);
	$uploadOk = 1;
	$addition = 1;

	while (file_exists($target_file)) {
	    $pathparts = pathinfo($target_file);
	    $target_file = $pathparts["dirname"] . "/" . $pathparts["filename"] . "-" . $addition . "." . $pathparts["extension"];
	    $addition = $addition + 1;
	}

	move_uploaded_file($_FILES["primaryInputFile"]["tmp_name"], $target_file);

	$request->sbolFile = $target_file;
} else {
	$target_dir = "uploads/";
	$target_file = $target_dir . uniqid() . ".upload";
	$uploadOk = 1;
	$addition = 1;
	echo $target_file;

	while (file_exists($target_file)) {
	    $pathparts = pathinfo($target_file);
	    $target_file = $pathparts["dirname"] . "/" . $pathparts["filename"] . "-" . $addition . "." . $pathparts["extension"];
	    $addition = $addition + 1;
	}

	file_put_contents($target_file, $_POST["pasteInputFile"]);

	$request->sbolFile = $target_file;
}
if(isset($_FILES["diffInputFile"])) {
	$target_dir = "uploads/";
	$target_file = $target_dir . basename($_FILES["diffInputFile"]["name"]);
	$uploadOk = 1;
	$addition = 1;

	while (file_exists($target_file)) {
	    $pathparts = pathinfo($target_file);
	    $target_file = $pathparts["dirname"] . "/" . $pathparts["filename"] . "-" . $addition . "." . $pathparts["extension"];
	    $addition = $addition + 1;
	}

	move_uploaded_file($_FILES["diffInputFile"]["tmp_name"], $target_file);

	$request->comparisonFile = $target_file;
}

$request->fileOptions->fill(isset($_POST["convertSbol11To20"]), isset($_POST["convertSbol20ToGenBank"]), 
							isset($_POST["convertGenBankToSbol20"]), $_POST["genBankComponentDefinition"]);

$request->conversionOptions->fill($_POST["uriPrefix"], $_POST["version"]);

$request->validationOptions->fill(isset($_POST["allowNonCompliantUris"]), isset($_POST["allowIncompleteDocuments"]),
									isset($_POST["checkBestPractices"]), isset($_POST["failOnFirstError"]),
									isset($_POST["displayFullStackTrace"]));

if($request->fileOptions->convertSbol20ToGenBank) {
	$pathparts = pathinfo($request->sbolFile);
	$request->outputFile = $pathparts["dirname"] . "/" . $pathparts["filename"] . "-output.gb";
} else {
	$pathparts = pathinfo($request->sbolFile);
	$request->outputFile = $pathparts["dirname"] . "/" . $pathparts["filename"] . "-output.xml";
}

$request->result->setOutput($request->executeValidation());

$_SESSION["request"] = serialize($request);


var_dump($_POST);
var_dump($request);

//echo $request->generateCommand();
header("Location: result.php");

?>