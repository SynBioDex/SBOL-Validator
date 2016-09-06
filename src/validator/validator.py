## SBOL Validator worker class
## Written by Zach Zundel
## zach.zundel@utah.edu
## 08/13/2016
import subprocess
from os.path import base


class ValidationResult:
    valid = False
    errors = []

    def __init__(self, output_file):
        self.output_file = output_file

    def digest_errors(self, output):
        errors = [output,]

    def decipher(self, output):
        if "Validation failed." in output:
            self.valid = False
            self.digest_errors(output)
        else:
            self.valid = True

    def broken_validation_request(self):
        self.valid = False
        self.errors = ["Something about your validation request is contradictory or poorly-formed."]


class ValidationRun:
    def __init__(self, options, validation_file, diff_file=None):
        self.options = options
        self.validation_file = validation_file
        self.diff_file = diff_file

    def execute(self):
        result = ValidationResult(self.options.output_file)
        try:
            command = self.options.command("libSBOLj.jar", self.validation_file, self.diff_file)
        except ValueError:
            result.broken_validation_request()

        # Attempt to run command
        try:
            output = subprocess.run(command, universal_newlines=True, stdout=subprocess.PIPE, stderr=subprocess.STDOUT, check=True)
        except subprocess.CalledProcessError as e:
            #If the command fails, the file is not valid.
            result.valid = False;
            result.errors += [e.output, ]    

        result.decipher(output.stdout)
        return result


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

    def build(self, data):
        for key, value in data.items():
            setattr(self, key, value)

    def command(self, jar_path, validation_file, diff_file=None):
        command = ["java", "-jar", jar_path, validation_file.name, "-o", self.output_file, "-l", self.language]

        if self.test_equality and diff_file:
            command += ["-e", diff_file.name]
            return command
        elif self.test_equality and not diff_file:
            raise ValueError

        if self.subset_uri:
            command += ["-s", self.subset_uri]
        
        if self.continue_after_first_error and not self.provide_detailed_stack_trace:
            command += ["-f"]
        elif self.continue_after_first_error and self.provide_detailed_stack_trace:
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

        
        
        
