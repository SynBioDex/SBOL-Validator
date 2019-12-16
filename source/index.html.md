---
title: API Reference

language_tabs: # must be one of https://git.io/vQNgJ
  - python
  - javascript

toc_footers:
  - <a href='http://www.async.ece.utah.edu/tools/sbol-validatorconverter'>About the SBOL Validator</a>
  - <a href='https://validator.sbolstandard.org'>SBOL Validator</a>
  - <a href='https://github.com/synbiodex/sbol-validator'>GitHub</a>

search: false
---

# Introduction
Welcome to the SBOL Validation/Conversion API! This API allows for the validation of documents encoded using the [SBOL](https://sbolstandard.org) data standard.

# Validate/Convert File
### HTTP Request
`POST https://validator.sbolstandard.org/validate/`

```python
import requests


file = open("sequence1.xml").read()

request = { 'options': {'language' : 'GenBank',
                        'test_equality': False,
                        'check_uri_compliance': False,
                        'check_completeness': False,
                        'check_best_practices': False,
                        'fail_on_first_error': False,
                        'provide_detailed_stack_trace': False,
                        'subset_uri': '',
                        'uri_prefix': '',
                        'version': '',
                        'insert_type': False,
                        'main_file_name': 'main file',
                        'diff_file_name': 'comparison file',
                                },
            'return_file': True,
            'main_file': file
          }


resp = requests.post("https://validator.sbolstandard.org/validate/", json=request)
```

```javascript
request({ method: 'POST',
              uri: 'http://validator.sbolvalidator.org/validate/',
              'content-type': 'application/json',
              json: { 'options': {  'language' : 'GenBank',
                                'test_equality': false,
                                'check_uri_compliance': false,
                                'check_completeness': false,
                                'check_best_practices': false,
                                'fail_on_first_error': false,
                                'provide_detailed_stack_trace': false,
                                'subset_uri': '',
                                'uri_prefix': '',
                                'version': '',
                                'insert_type': false,
                                'main_file_name': 'main file',
                                'diff_file_name': 'comparison file',
                            },
                'return_file': true,
                'main_file': file
                }
            }, function(err, response, body) {}
        );

```

> The above command returns JSON structured like this

```
{
  "valid": true,
  "equality": false,
  "errors": [""],
  "output_file": "http://www.async.ece.utah.edu/work/sequence1.gb"
}
```

### Query Parameters
Parameter | Required | Default | Description
--- | --- | --- | ---
options | yes | N/A | A dictionary containing the validation/conversion options, detailed below
return_file | no | false | Whether or not to return the file contents as a string
main_file | yes | N/A | A string containing the contents for the main file of the validation request
diff_file | no | N/A | A string containing the contents for the file to be compared against the main_file

### Options
Parameter | Default | Description
--- | --- | ---
language | “SBOL2” | Selects the output file format from “SBOL2”, “SBOL1”, “GenBank”, “FASTA”, and “GFF3”
test_equality | false | If set to true, a diff_file is required and the main_file and diff_file will be compared
check_uri_compliance | true | If set to false, URIs in the file will not be checked for compliance with the SBOL specification
check_completeness | true | If set to false, not all referenced objects must be described within the given main_file
check_best_practices | false | If set to true, the file is checked for the best practice rules set in the SBOL specification
fail_on_first_error | false | If set to true, the validator will fail at the first error
provide_detailed_stack_trace | false | If set to true (and fail_on_first_error is true) the validator will provide a stack trace for the first validation error
subset_uri | none | A URI of a TopLevel described in the given SBOL file – the output will be only that TopLevel and its described children
uri_prefix | none | Required for conversion from FASTA and GenBank to SBOL1 or SBOL2, used to generate URIs
version | none | Adds the version to all URIs and to the document
insert_type | false | Inserts type into the URIs of objects
main_file_name | “main file” | The name of the main file, only used for reporting differences between two files when test_equality is true
diff_file_name | “comparison file” | The name of the secondary file (diff_file), only used in reporting differences between two files when test_equality is true

## Response
Parameter | Default | Description
--- | --- | ---
valid | false | Returns true if the file given is valid SBOL, or can be converted to valid SBOL
check_equality | false | Returns true if a comparison was run
equality | false | Returns true if the two files are equal, false otherwise. Only returned if check_equality is true
errors | [“] | ‘errors’ is a bit of a misnomer, this is simply the output of running the validation split by lines
output_file | N/A | The URI of the file requested
result | file contents | The value of the output file, Only returned if return_file is true in the validation request

