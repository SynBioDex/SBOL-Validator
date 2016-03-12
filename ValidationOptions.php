<?php
class ValidationOptions {

	var $allowNonCompliantUri;
	var $allowIncompleteDocument;
	var $checkBestPractices;
	var $failOnFirstError;
	var $displayFullStackTrace;

	function __construct() {
		$this->allowNonCompliantUri = false;
		$this->allowIncompleteDocument = false;
		$this->checkBestPractices = false;
		$this->failOnFirstError = false;
		$this->displayFullStackTrace = false;
	}

	function fill($allowNonCompliantUri, $allowIncompleteDocument,
					$checkBestPractices, $failOnFirstError, 
					$displayFullStackTrace) {
		$this->allowNonCompliantUri = $allowNonCompliantUri;
		$this->allowIncompleteDocument = $allowIncompleteDocument;
		$this->checkBestPractices = $checkBestPractices;
		$this->failOnFirstError = $failOnFirstError;
		$this->displayFullStackTrace = $displayFullStackTrace;
	}

	function generateCommandFragment() {
		$fragment = "";

		if($this->allowNonCompliantUri) {
			$fragment = $fragment . "-n ";
		}

		if($this->allowIncompleteDocument) {
			$fragment = $fragment . "-i ";
		}

		if($this->checkBestPractices) {
			$fragment = $fragment . "-b ";
		}

		if($this->failOnFirstError) {
			$fragment = $fragment . "-d ";
		}

		if($this->displayFullStackTrace) {
			$fragment = $fragment . "-f ";
		}

		return $fragment;
	}
}
?>