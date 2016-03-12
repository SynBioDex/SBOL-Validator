<?php
class ValidationResult {
	var $output;
	var $successful;

	function __construct() {
		$this->output = "";
		$this->successful = false;
	}

	function setOutput($output) {
		$this->output = nl2br($output);
		if(strpos(strtolower($output), "successful") !== false) {
			$this->successful = true;
		}
	}
}
?>