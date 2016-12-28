# SBOL Validator
A web-based validator for SBOL files backed by libSBOLj's validation runtimes. This validator offers support for SBOL2, SBOL1.1, and GenBank.
Furthermore, it is accessible through a web GUI or a RESTful API. 

The validator is currently live [here](http://www.async.ece.utah.edu/validator/) with an API endpoint found at `http://www.async.ece.utah.edu/validate/`.

### Installation
##### System Requirements
Coming soon.

##### Installation Process
Coming soon.

### API
The API for the validator is a RESTful API which permits programmatic access to the validation runtimes. The validator can be set up at 
##### Setup
The API installation and the validator installation are coupled, in that if you have an accessible and working validator then the API has also been successfully installed. 
##### Usage
The API accepts a JSON object containing between three and four top-level members. One to two of these members are files, which should be passed as base-64 encoded strings with the names `main_file` or `diff_file`. `main_file` is always required, and `diff_file` is required if the `test_equality` validation option is selected. Additionally, there is a boolean `wantFileBack` which is required and will dictate whether a file or a simple success message is returned if validation is successful.

###### Validation Options
Below is an example of a valid JSON top-level `options` object. All values are required.
```
"options" : {"language": "SBOL1",
                       "test_equality": false,
                       "check_uri_compliance": false,
                       "check_completeness": false,
                       "check_best_practices": false,
                       "continue_after_first_error": false,
                       "provide_detailed_stack_trace": false,
                       "subset_uri": "",
                       "uri_prefix": "",
                       "version": "",
                       "insert_type": false;
                       "main_file_name": "main file",
                       "diff_file_name": "diff file", 
                       }
```

The options here correspond to the form options of the validation browser application, which should be referred to in order to understand each of their significances. The `language` flag has the following options: `SBOL1`, `SBOL2`, `GenBank`, `FASTA`. Please note that all fields and values are case-sensitive.

##### Example
```python
import requests


request = {"options": {"language": "SBOL1",
                       "test_equality": False,
                       "check_uri_compliance": False,
                       "check_completeness": False,
                       "check_best_practices": False,
                       "continue_after_first_error": False,
                       "provide_detailed_stack_trace": False,
                       "subset_uri": "",
                       "uri_prefix": "",
                       "version": "",
                       "insert_type": False,
                       "main_file_name": "main file",
                       "diff_file_name": "diff file", 
                       },
           "return_file": True,
           "main_file": open("sequence1.xml").read()}

resp = requests.post("http://www.async.ece.utah.edu/validate/", json=request)
```
This Python example prepares a JSON object, adds the base64-encoded string of the SBOL file, and POSTs the request to the specified endpoint.

### Browser Usage
To connect to the browser application, simply open a browser and navigate to the root directory. Upload a file for validation and select the desired options.
