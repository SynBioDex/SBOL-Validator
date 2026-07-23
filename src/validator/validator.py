## SBOL Validator worker class
## Written by Zach Zundel
## zach.zundel@utah.edu
## 08/13/2016
import subprocess
import uuid
import traceback
import os
import logging
import sys

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    handlers=[logging.StreamHandler(sys.stdout)]
)
logger = logging.getLogger(__name__)

class ValidationResult:
    # Status lines the SBOL-Converter prints that are not validation errors and should
    # not surface to the user (e.g. among a comparison's differences).
    STATUS_LINES = frozenset([
        "Validation successful, no errors.",
        "Either Validate or Convert to/from SBOL2",
    ])

    def __init__(self, output_file, equality):
        self.check_equality = equality
        self.output_file = output_file
        self.valid = False
        self.errors = []

    def digest_errors(self, output):
        self.errors = [
            line for line in output.strip().split('\n')
            if line.strip() and line.strip() not in self.STATUS_LINES
        ]

    def decipher(self, output, options):
        if self.check_equality:
            if "differ" in output or "not found in" in output:
                self.equal = False
            else:
                self.equal = True

        succeeded = "Validation successful, no errors." in output

        # The SBOL-Converter is silent on a successful conversion (e.g. SBOL2<->SBOL3)
        # and exits non-zero on any error, so reaching this point with a written
        # output file signals success even without the validation message.
        if not succeeded and options.return_file:
            succeeded = os.path.exists(options.output_file) and os.path.getsize(options.output_file) > 0

        if succeeded:
            self.digest_errors(output)
            self.valid = True

            if options.return_file and os.path.exists(options.output_file):
                with open(options.output_file, 'r') as file:
                    self.result = file.read()
        else:
            self.valid = False
            self.digest_errors(output)

    def broken_validation_request(self, command):
        self.valid = False
        self.errors = ["Something about your validation request is contradictory or poorly-formed!", " ".join(command)]

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
            command = self.options.command("sbol-converter.jar", self.validation_file, self.diff_file)
            logger.info("Running command: %s", " ".join(command))
            try:
                output = subprocess.check_output(command, universal_newlines=True, stderr=subprocess.STDOUT)
            except subprocess.CalledProcessError as exception:
                # The SBOL-Converter exits non-zero both for an invalid document and when a
                # comparison finds differences; decipher classifies each from the output.
                output = exception.output
            result.decipher(output, self.options)
        except ValueError as ve:
            print(traceback.print_tb(ve.__traceback__))
            result.broken_validation_request(command)

        return result.json()


class ValidationOptions:
    language = "SBOL2"
    subset_uri = False
    fail_on_first_error = False
    provide_detailed_stack_trace = False
    check_uri_compliance = True
    check_completeness = True
    check_best_practices = False
    uri_prefix = False
    version = False
    insert_type = False
    test_equality = False
    return_file = True
    main_file_name = "main file"
    diff_file_name = "comparison file"

    def __init__(self, return_file):
        self.return_file = return_file

    def build(self, data):
        for key, value in data.items():
            setattr(self, key, value)
        self.output_file = os.path.join('work', str(uuid.uuid4()))

        if self.language in ['SBOL1', 'SBOL2', 'SBOL3', 'SBML']:
            self.output_file = self.output_file + ".rdf"
        elif self.language == 'GFF3':
            self.output_file = self.output_file + '.gff'
        elif self.language == 'GenBank':
            self.output_file = self.output_file + '.gb'
        else:
            self.output_file = self.output_file + '.fasta'

    def command(self, jar_path, validation_file, diff_file=None):
        command = ["/usr/bin/java", "-jar", jar_path, validation_file, "-o", self.output_file, "-l", self.language]

        if self.test_equality and diff_file:
            command += ["-e", diff_file, "-mf", self.main_file_name, "-cf", self.diff_file_name]
        elif self.test_equality and not diff_file:
            raise ValueError

        if self.subset_uri:
            command += ["-s", self.subset_uri]

        if self.provide_detailed_stack_trace and not self.fail_on_first_error:
            raise ValueError

        if self.fail_on_first_error:
            command += ["-f"]

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
