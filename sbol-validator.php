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
        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }

        $movefile = wp_handle_upload($_FILES["fileToUpload"]["tmp_name"]);
        var_dump($movefile);
        if ($movefile && !isset($movefile['error'])) {
            $pathparts = pathinfo($movefile['file']);
            $command = "java -jar " . plugin_dir_path( __FILE__ ) . "libSBOLj-2.0.0-withDependencies.jar ";
            $command = $command . $movefile['file'] . " ";
            $command = $command . $pathparts['filename'] . "-validated." . $pathparts['extension'] . " ";

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
            echo $command;
            echo shell_exec($command);
    		//$zip->open($target_zip, ZipArchive::CREATE);
    		//$zip->addFile( $target_file . '.validated.sbol', $target_file  . '.rdf');
    		echo "<br>";
    		echo '<a href="' . site_url() . '/' . $target_file . '-validated.' . $extension . '">Converted and adjusted SBOL</a>';
        } 
        else {
                echo "Sorry, there was an error uploading your file.";
        }
    }
}
function sbolvalidator_shortcode(){
	validate();
	sbolvalidator_html_form();
}

add_shortcode( 'sbolvalidator', 'sbolvalidator_shortcode' );
?>
