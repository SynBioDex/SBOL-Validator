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
	echo "<form action=\"" . esc_url($_SERVER['REQUEST_URI']) . "\" method=\"post\" enctype=\"multipart/form-data\">";
	echo "Select SBOL file to validate: ";
	echo "<input type=\"file\" name=\"fileToUpload\" id=\"fileToUpload\">";
	echo "<br>";
	echo "OR input SBOL file to validate: ";
	echo "<input type=\"textarea\" name=\"textToUpload\" id=\"textToUpload\">";
	echo "<br>";
	echo "Allow noncompliance: ";
	echo "<input type=\"checkbox\" name=\"noncompliant\" id=\"noncompliant\">";
	echo "<br>";
	echo "Allow incompleteness: ";
	echo "<input type=\"checkbox\" name=\"incomplete\" id=\"incomplete\">";
	echo "<br>";
	echo "Check best practices: ";
	echo "<input type=\"checkbox\" name=\"best\" id=\"best\">";
	echo "<br>";
	echo "Include top-levels in URI: ";
	echo "<input type=\"checkbox\" name=\"toplevel\" id=\"toplevel\">";
	echo "<br>";
	echo "Output validated file as GenBank: ";
	echo "<input type=\"checkbox\" name=\"genbank\" id=\"genbank\">";
	echo "Enter a URI prefix for 1.0 to 2.0 conversion if desired:";
	echo "<input type=\"text\" name=\"garg\" id=\"garg\">";
	echo "<br>";
	echo "Enter a URI prefix for 1.0 to 2.0 conversion if desired:";
	echo "<input type=\"text\" name=\"prefix\" id=\"prefix\">";
	echo "<br>";
	echo "<input type=\"submit\" value=\"Upload for Validation\" name=\"submit\">";
	echo "</form>";
}

//Upload file and run validation
function sbolvalidator_validate()
{
	//Import Wordpress upload functions
	if (!function_exists('wp_handle_upload')) {
		require_once(ABSPATH . 'wp-admin/includes/file.php');
	}
	//Allow upload without form and allow upload of non-media filetypes
	$upload_overrides = array('test_form' => false, 'mimes' => array(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION) => $_FILES["fileToUpload"]["type"]));
	
	$movefile = [];


	if(isset($_POST["textToUpload"])) {
		file_put_contents("/var/www/html/temp/input", $_POST["textToUpload"]);
		$filepath = "/var/www/html/temp/input";
	}
	else {
		//Add file to Wordpress uploads
		$movefile = wp_handle_upload($_FILES["fileToUpload"], $upload_overrides);
		$filepath = $movefile["path"];
	}


	//If upload is successful, run validation. Otherwise, explain failure.
	if ($movefile && !isset($movefile['error'])) {
		//Build shell command from form
		$pathparts = pathinfo($filepath);
		$command = "java -jar " . plugin_dir_path(__FILE__) . "libSBOLj-2.0.1-SNAPSHOT-withDependencies.jar ";
		$command = $command . $movefile['file'] . " ";
		$command = $command . "-o " . $pathparts['filename'] . "-validated." . $pathparts['extension'] . " ";
		if (isset($_POST["noncompliant"])) {
			$command = $command . "-n ";
		}
		if (isset($_POST["incomplete"])) {
			$command = $command . "-i ";
		}
		if ($_POST["prefix"] != "") {
			$command = $command . "-p " . escapeshellarg($_POST["prefix"]) . " ";
		}
		if(isset($_POST["best"])) {
			$command = $command . "-b "; 
		}
		if(isset($_POST["toplevel"])) {
			$command = $command . "-t "; 
		}
		if (isset($_POST["genbank"]) && $_POST["garg"] != "") {
			$command = $command . "-g " . escapeshellarg($_POST["garg"]) . " ";
		}
		$command = $command . '2>&1';
		echo $command;
		//Execute shell command
		$result = shell_exec($command);
		
		//Print result, and if necessary, print link to valid SBOL
		echo $result;
		if (trim($result) == "Validation successful, no errors.") {
			echo "<br>";
			echo '<a href="' . $movefile["url"] . '">Converted and adjusted SBOL</a>';
		}
	} else {
		echo "Sorry, there was an error uploading your file. <br><b>Wordpress says: " . $movefile['error'] . '</b>';
	}
}

//If a POST request has been submitted, run validate method. Otherwise, display form.
function sbolvalidator_shortcode()
{
	echo "testing for java";
	if(sbolvalidator_javatest() == "yes") {
		echo "testing for submission";
		if (isset($_POST["submit"])) {
			echo "Something ws submitted?";
			sbolvalidator_validate();
			echo "<hr>";
		}
		echo "form was drawn!";
		sbolvalidator_html_form();
	} else {
		echo "Sorry, it appears that your server does not allow Wordpress to run Java.";
	}
}

//Test for java 
function sbolvalidator_javatest() {
	return exec('java -version > NUL && echo yes || echo no');
}


//Add shortcode for Wordpress use
add_shortcode('sbolvalidator', 'sbolvalidator_shortcode');
?>
