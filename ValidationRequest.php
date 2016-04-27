<?php

require_once("FileOptions.php");
require_once("ConversionOptions.php");
require_once("ValidationOptions.php");
require_once("ValidationResult.php");

class ValidationRequest {
	var $sbolFile;
	var $comparisonFile;
	var $outputFile;
	var $fileOptions;
	var $conversionOptions;
	var $validationOptions;
	var $result;

	function __construct() {
		$this->fileOptions = new FileOptions();
		$this->conversionOptions = new ConversionOptions();
		$this->validationOptions = new ValidationOptions();
		$this->result = new ValidationResult();
	}

	function executeValidation() {
		return shell_exec($this->generateCommand());
	}

	function generateCommand() {
		$command = "java -jar libSBOLj-2.0.1-SNAPSHOT-withDependencies.jar \"" . $this->sbolFile . "\" ";

		if($this->fileOptions->performFileDiff) {
			$command = $command . "-e \"" . $this->comparisonFile . "\" ";
		}

		$command = $command . $this->fileOptions->generateCommandFragment();
		$command = $command . $this->conversionOptions->generateCommandFragment();
		$command = $command . $this->validationOptions->generateCommandFragment();

		$command = $command . "-o \"" . $this->outputFile . "\" ";

		return $command . "2>&1";
	}

	function insertStringIntoSbolFilename($addition) {
		$pathparts = pathinfo($this->sbolFile);
    	return $target_file = $pathparts["dirname"] . "/" . $pathparts["filename"] . "-" . $addition . "." . $pathparts["extension"];
	}

	function downloadCheck() {
		return true;
	}

}

?>