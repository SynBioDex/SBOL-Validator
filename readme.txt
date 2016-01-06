=== SBOL Validator ===
Contributors: 16zzundel5
Tags: sbol, validator, validation, biology, 
Requires at least: 1.0
Tested up to: 4.4
Stable tag: trunk

Validates SBOL 2.0 files and converts SBOL 1.0 to 2.0.

== Description ==
This plugin provides a shortcode which creates a form that accepts SBOL files and validates them according to the SBOL Standard available at www.sbolstandard.org. If the file is in SBOL 2.0, it is validated and outputted, but if the file is in a lower version of SBOL, then it is converted, validated, and outputted as SBOL 2.0. This plugin is powered by the libSBOLj library.

== Installation ==
Java must be enabled on the server and accessible via the command line for this plugin to function properly.