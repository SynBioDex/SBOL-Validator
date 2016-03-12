<?php
class FileOptions {

	var $convertSbol11To20;
	var $convertSbol20ToGenBank;
	var $convertGenBankToSbol20;
	var $componentDefinitionUri;

	function __construct() {
		$this->convertSbol11To20 = false;
		$this->convertSbol20ToGenBank = false;
		$this->convertGenBankToSbol20 = false;
		$this->componentDefinitionUri = false;
	}

	function fill($convertSbol11To20, $convertSbol20ToGenBank, 
					$convertGenBankToSbol20, $componentDefinitionUri) {
		$this->convertSbol11To20 = $convertSbol11To20;
		$this->convertSbol20ToGenBank = $convertSbol20ToGenBank;
		$this->convertGenBankToSbol20 = $convertGenBankToSbol20;

		if($componentDefinitionUri != "") {
			$this->componentDefinitionUri = escapeshellarg($componentDefinitionUri);
		}
	}

	function generateCommandFragment() {
		$fragment = "";

		if($this->convertGenBankToSbol20) {
			$fragment = $fragment . "-g ";
		}

		if($this->componentDefinitionUri && $this->convertSbol20ToGenBank) {
			$fragment = $fragment . "-c " . $this->componentDefinitionUri . " ";
		} else if(!$this->componentDefinitionUri && $this->convertSbol20ToGenBank) {
			$fragment = $fragment . "-r ";
		}

		return $fragment;
	}

	function downloadCheck() {
		return $this->convertSbol11To20 || $this->convertSbol20ToGenBank || $this->convertGenBankToSbol20;
	}
}
?>