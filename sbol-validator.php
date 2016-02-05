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
	$upload_overrides = array('test_form' => false, 'mimes' => array(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION) => $_FILES["fileToUpload"]["type"]));
	
	$movefile = [];
	$uploaded = false;
	$filepath = "";

	if($_POST["textToUpload"] != "") {
		file_put_contents("/var/www/html/temp/input", $_POST["textToUpload"]);
		$filepath = "/var/www/html/temp/input";
		$uploaded = true;
	}
	else {
		//Add file to Wordpress uploads
		$movefile = wp_handle_upload($_FILES["fileToUpload"], $upload_overrides);
		if($movefile && !isset($movefile['error'])) {
			$filepath = $movefile['file'];
			$uploaded = true;
		}
	}


	//If upload is successful, run validation. Otherwise, explain failure.
	if ($uploaded) {
		//Build shell command from form
		$pathparts = pathinfo($filepath);
		$command = "java -jar " . plugin_dir_path(__FILE__) . "libSBOLj-2.0.1-SNAPSHOT-withDependencies.jar ";
		$command = $command . $filepath . " ";
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
		$command = $command . '> output.txt 2>&1';

		//Execute shell command
		$result = shell_exec($command);
		
		//Print result, and if necessary, print link to valid SBOL
		echo file_get_contents("output.txt");
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


//Add shortcode for Wordpress use
add_shortcode('sbolvalidator', 'sbolvalidator_shortcode');
?>
