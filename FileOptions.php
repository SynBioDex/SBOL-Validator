<?php
class FileOptions {

	var $output;
	var $topLevelToConvert;
	var $performFileDiff;

	function __construct() {
		$this->output = "";
		$this->toplevel = false;
		$this->performFileDiff = false;
	}

	function fill($output, $topLevelToConvert, $performFileDiff) {
		$this->output = $output;
		$this->performFileDiff = $performFileDiff;

		if($topLevelToConvert != "") {
			$this->topLevelToConvert = escapeshellarg($topLevelToConvert);
		}
	}

	function generateCommandFragment() {
		$fragment = "";

		if($this->topLevelToConvert != "") {
			$fragment = $fragment . "-s " . $this->topLevelToConvert . " ";
		}

		$fragment = $fragment . "-l " . $this->output . " ";

		return $fragment;
	}
}
?>
