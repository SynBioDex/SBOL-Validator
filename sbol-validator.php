<?php
/*
Plugin Name: SBOL Validator
Plugin URI:  https://github.com/SynBioDex/SBOL-Validator
Description: Validates 
Version:     1.2
Author:      Zach Zundel
Author URI:  http://www.async.ece.utah.edu/~zachz
 */


$java = "no";

//Print the form
function sbolvalidator_html_form()
{
	$dir = pathinfo(__FILE__)["dirname"];
	$form = file_get_contents($dir . "/form.html");
	$css = file_get_contents($dir . "/view.css"); 
	echo "<style>" . $css . "</style>";
	echo "<img src=\"http://www.async.ece.utah.edu/wp-content/plugins/sbol-validator/top.png\">";
	echo $form;
	echo "<img src=\"http://www.async.ece.utah.edu/wp-content/plugins/sbol-validator/bottom.png\">";
}

//Upload file and run validation
function sbolvalidator_validate()
{
	//Import Wordpress upload functions
	if (!function_exists('wp_handle_upload')) {
		require_once(ABSPATH . 'wp-admin/includes/file.php');
	}
	//Allow upload without form and allow upload of non-media filetypes
	$upload_overrides = array('test_form' => false, 'mimes' => array(pathinfo($_FILES["sbol_file"]["name"], PATHINFO_EXTENSION) => $_FILES["sbol_file"]["type"]));
	
	$movefile = [];
	$uploaded = false;
	$filepath = "";

	if($_POST["textToUpload"] != "") {
		$movefile = wp_handle_bits("input.xml", $_POST["textToUpload"]);
	}
	else {
		//Add file to Wordpress uploads
		$movefile = wp_handle_upload($_FILES["sbol_file"], $upload_overrides);
	}
		if($movefile && !isset($movefile['error'])) {
			$filepath = $movefile['file'];
			$uploaded = true;
		}


	//If upload is successful, run validation. Otherwise, explain failure.
	if ($uploaded) {
		//Build shell command from form
		$wants20 = false;
		$pathparts = pathinfo($movefile["file"]);
		$command = "java -jar " . plugin_dir_path(__FILE__) . "libSBOLj-2.0.1-SNAPSHOT-withDependencies.jar ";
		$command = $command . $filepath . " ";
		$command = $command . "-o " $pathparts['dirname'] . "/". $pathparts['filename'] . "-validated.";
		if (isset($_POST["20togb"]) && $_POST["cdUri"] != "") {
			$command = $command . "gb ";
		} else {
			$command = $command . $pathparts['extension'] . " ";
		}
		if (isset($_POST["11to20"])) {
			$wants20 = true;
		}
		if (isset($_POST["gbto20"])) {
			$command = $command . "-g ";
		}
		if (isset($_POST["20togb"]) && $_POST["cdUri"] != "") {
			$command = $command . "-c " . $_POST["cdUri"] . " ";
		}
		if (isset($_POST["noncompliant"])) {
			$command = $command . "-n ";
		}
		if (isset($_POST["incomplete"])) {
			$command = $command . "-i ";
		}
		if ($_POST["uriForConversion"] != "") {
			$command = $command . "-p " . escapeshellarg($_POST["uriForConversion"]) . " ";
		}
		if($_POST["version"] != "") {
			$command = $command . "-v " . escapeshellarg($_POST["version"]) . " ";
		}
		if (isset($_POST["uriType"])) {
			$command = $command . "-t ";
		}
		if(isset($_POST["bestPractices"])) {
			$command = $command . "-b "; 
		}
		if(isset($_POST["failOnFirst"])) {
			$command = $command . "-f ";
		}
		if(isset($_POST["fullStack"]) && isset($_POST["failOnFirst"])) {
			$command = $command . "-d ";
		}
		$command = $command . '> output.txt 2>&1';
		
		//Execute shell command
		$result = shell_exec($command);
		$result = file_get_contents("output.txt");
	
		//Print result, and if necessary, print link to valid SBOL
		echo $result;
		
		if (startsWith(trim($result), "Converting SBOL Version 1 to SBOL Version 2") && $wants20 && strpos($result, 'Validation failed') !== false) {
			echo "<br>";
			echo '<a href="' . returnUrlWithoutExtension($movefile["url"]) . '-validated.' . $pathparts["extension"] . '>Converted and adjusted SBOL</a>';
		}
		if(isset($_POST["20togb"]) && $_POST["cdUri"] != "") {
			echo "<br>";
			echo '<a href="'. returnUrlWithoutExtension($movefile["url"]) . '-validated.gb">Converted GenBank file</a>';
		}
		
	} else {
		echo "Sorry, there was an error uploading your file. <br><b>Wordpress says: " . $movefile['error'] . '</b>';
	}
}

//If a POST request has been submitted, run validate method. Otherwise, display form.
function sbolvalidator_shortcode()
{
	$_POST = array_map('stripslashes_deep', $_POST);
	if(sbolvalidator_javatest() == "yes") {
		if (isset($_POST["submit"])) {
			sbolvalidator_validate();
			echo "<hr>";
		}
		sbolvalidator_html_form();
	} else {
		echo "Sorry, it appears that your server does not allow Wordpress to run Java.";
	}
}

//Test for java 
function sbolvalidator_javatest() {
	return exec('java -version > NUL && echo yes || echo no');
}

function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

function returnUrlWithoutExtension($string) {
	$extensionPosition = strpos($string, ".", strlen($string) - 10);
	return substr($string, 0, $extensionPosition);
}

//Add shortcode for Wordpress use
add_shortcode('sbolvalidator', 'sbolvalidator_shortcode');
?>
