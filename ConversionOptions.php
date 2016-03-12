<?php
class ConversionOptions {

	var $uriPrefix;
	var $version;

	function __construct() {
		$this->uriPrefix = "";
		$this->version = "";
	}

	function fill($uriPrefix, $version) {
		if($uriPrefix != "") {
			$this->uriPrefix = escapeshellarg($uriPrefix);
		} else {
			$this->uriPrefix = false;
		}

		if($version != "") {
			$this->version = escapeshellarg($version);
		} else {
			$this->version = false;
		}
	}

	function generateCommandFragment() {
		$fragment = "";

		if($this->uriPrefix) {
			$fragment = $fragment . "-p " . $this->uriPrefix . " "; 
		}

		if($this->version) {
			$fragment = $fragment . "-v " . $this->version . " "; 
		}

		return $fragment;
	}
}
?>