<?php
/*
Plugin Name: SBOL Validator
Plugin URI: http://async.ece.utah.edu/validator
Description: This plugin validates SBOL files
Version: 1.0
Author: Zach Zundel
*/

function sbolvalidator_html_form() {
	// Print the HTML for the form
    if(empty($_POST["submit"])) {
    	echo "<form action=\"" . esc_url( $_SERVER['REQUEST_URI'] ) . "\" method=\"post\" enctype=\"multipart/form-data\">";
    	echo "Select SBOL file to validate:";
    	echo "<input type=\"file\" name=\"fileToUpload\" id=\"fileToUpload\">";
    	echo "<br>";
    	echo "Allow noncompliance: ";
    	echo "<input type=\"checkbox\" name=\"noncompliant\" id=\"noncompliant\">";
    	echo "<br>";
    	echo "Allow incompleteness:";
    	echo "<input type=\"checkbox\" name=\"incomplete\" id=\"incomplete\">";
    	echo "<br>";
    	echo "Enter a URI prefix for 1.0 to 2.0 conversion if desired:";
    	echo "<input type=\"text\" name=\"prefix\" id=\"prefix\">";
    	echo "<br>";
    	echo "<input type=\"submit\" value=\"Upload for Validation\" name=\"submit\">";
    	echo "</form>";
    }
}

function validate() {
    if(isset($_POST["submit"])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . uniqid();
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	echo 'before';
	$zip = new ZipArchive;
	echo 'after';
	$target_zip = $target_file . '.zip';
	$target_file = $target_file . '.sbol';
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } 
        else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                $command = "java -jar /home/tang/zachz/Downloads/libSBOLj-2.0.0-withDependencies.jar ";
                $command = $command . "/home/tang/zachz/public_html/" . $target_file . " ";
	        $command = $command . "-o /home/tang/zachz/public_html/" . $target_file . ".validated.sbol ";

                if(isset($_POST["noncompliance"])) {
                    $command = $command . "-n ";
                }

                if(isset($_POST["incomplete"])) {
                    $command = $command . "-i ";
                }
                if($_POST["prefix"] != "") {
                    $command = $command . "-p " . escapeshellarg($_POST["prefix"]) . " ";
                }
		$command = $command . '2>&1';
                echo shell_exec($command);
		//$zip->open($target_zip, ZipArchive::CREATE);
		//$zip->addFile( $target_file . '.validated.sbol', $target_file  . '.rdf');
		echo "<br>";
		echo '<a href="' . site_url() . '/' . $target_zip . '">Download</a>';
            } 
            else {
                    echo "Sorry, there was an error uploading your file.";
            }
        }
    }
}

function sbolvalidator_shortcode(){
	validate();
	sbolvalidator_html_form();
}

add_shortcode( 'sbolvalidator', 'sbolvalidator_shortcode' );
?>
