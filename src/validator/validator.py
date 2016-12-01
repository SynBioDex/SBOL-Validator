## SBOL Validator worker class
## Written by Zach Zundel
## zach.zundel@utah.edu
## 08/13/2016
import subprocess
import uuid
import sys, os


class ValidationResult:
    def __init__(self, output_file, equality):
        self.equality = equality
        self.output_file = output_file
        self.valid = False
        self.errors = []

    def digest_errors(self, output):
        self.errors = filter(None, output.strip().strip(u'Validation failed.').split('\n'))

    def decipher(self, output):
        if "Validation successful, no errors." not in output:
            self.valid = False
            self.digest_errors(output)
        else:
            self.digest_errors(output.strip(u"Validation successful, no errors."))
            self.valid = True

    def broken_validation_request(self, command):
        self.valid = False
        self.errors = ["Something about your validation request is contradictory or poorly-formed.", " ".join(command)]

    def json(self):
        return self.__dict__

class ValidationRun:
    def __init__(self, options, validation_file, diff_file=None):
        self.options = options
        self.validation_file = validation_file
        self.diff_file = diff_file

    def execute(self):
        result = ValidationResult(self.options.output_file, self.options.test_equality)

	    # Attempt to run command
        try:
            command = self.options.command("libSBOLj.jar", self.validation_file, self.diff_file)
	        wd = os.path.join(os.path.abspath(os.sep), 'home', 'zach', 'SBOL-Validator', 'src');
            output = subprocess.check_output(command, universal_newlines=True, stderr=subprocess.STDOUT, cwd=wd)
            result.decipher(output)
        except subprocess.CalledProcessError as e:
            #If the command fails, the file is not valid.
            result.valid = False
            result.errors += [e.output, ]
        except ValueError:
            result.broken_validation_request(command)


        return result.json()


class ValidationOptions:
    language = "SBOL2"
    subset_uri = False
    continue_after_first_error = False
    provide_detailed_stack_trace = False
    check_uri_compliance = True
    check_completeness = True
    check_best_practices = False
    uri_prefix = False
    version = False
    insert_type = False
    test_equality = False
    main_file_name = "main file"
    diff_file_name = "comparison file"


    def build(self, data):
        for key, value in data.items():
            setattr(self, key, value)
        self.output_file = os.path.join('work', str(uuid.uuid4()))

        if self.language == 'SBOL2' or self.language == 'SBOL1':
            self.output_file = self.output_file + ".xml"
        elif self.language == 'GenBank':
            self.output_file = self.output_file + '.gb'
        else:
            self.output_file = self.output_file + '.fasta'

    def command(self, jar_path, validation_file, diff_file=None):
        command = ["usr/bin/java", "-jar", jar_path, validation_file, "-o", self.output_file, "-l", self.language]

        if self.test_equality and diff_file:
            command += ["-e", diff_file, "-mf", self.main_file_name, "-cf", self.diff_file_name]
            return command
        elif self.test_equality and not diff_file:
            raise ValueError

        if self.subset_uri:
            command += ["-s", self.subset_uri]
        
        if self.continue_after_first_error and not self.provide_detailed_stack_trace:
            command += ["-f"]
        elif not self.continue_after_first_error and self.provide_detailed_stack_trace:
            raise ValueError
        
        if self.provide_detailed_stack_trace:
            command += ["-d"]

        if not self.check_uri_compliance:
            command += ["-n"]
        
        if not self.check_completeness:
            command += ["-i"]

        if self.check_best_practices:
            command += ["-b"]

        if self.uri_prefix:
            command += ["-p", self.uri_prefix]
        
        if self.version:
            command += ["-v", self.version]

        if self.insert_type:
            command += ["-t"]

        return command

        
        
        
