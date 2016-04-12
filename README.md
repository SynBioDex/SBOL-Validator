# SBOL Validator
A web-based validator for SBOL files backed by libSBOLj's validation runtimes. This validator offers support for SBOL2, SBOL1.1, and GenBank.
Furthermore, it is accessible through a web GUI or a RESTful API. 

The validator is currently live [here](http://www.async.ece.utah.edu/sbol-validator) with an API endpoint found at `http://www.async.ece.utah.edu/sbol-validator/endpoint.php`.

### Installation
##### System Requirements
Your server mush have PHP5 installed and allow for the command `shell_exec()` and execution of Java on the command line by PHP. 
The `upload_max_filesize` PHP directive must be configured to allow uploads of whatever size you desire.

##### Installation Process
Installation of this validator is quite simple -- simply drop the contents of the directory into a directory on your webserver.
Ensure that there is an `uploads` folder in the root directory and you're good to go!

### API
The API for the validator is a RESTful API which permits programmatic access to the validation runtimes. The validator can be set up at 
##### Setup
The API installation and the validator installation are coupled, in that if you have an accessible and working validator then the API has also been successfully installed. 
##### Usage
The API accepts a JSON object containing between three and four top-level members. One to two of these members are files, which should be passed as base-64 encoded strings with the names `mainFile` or `diffFile`. `mainFile` is always required, and `difFile` is required if the `diff` validation option is selected. Additionally, there is a boolean `wantFileBack` which is required and will dictate whether a file or a simple success message is returned if validation is successful.

###### Validation Options
Below is an example of a valid JSON top-level `validationOptions` object. All values are required.
````
"validationOptions" : {"sbol11To20": false,
                       "sbol20ToGenBank": true,
                       "genBankToSbol20": false,
                       "diff": false,
                       "noncompliantUrisAllowed": false,
                       "incompleteDocumentsAllowed": false,
                       "bestPracticesCheck": false,
                       "failOnFirstError": false,
                       "displayFullErrorStackTrace": false,
                       "ComponentDefinitionUri": "",
                       "uriPrefix": "",
                       "version": "",
                       }
````

The options here correspond to the form options of the validation browser application, which should be referred to in order to understand each of their significances.

##### Example

```python
import requests
import base64

request = {"validationOptions": {"sbol11To20": False,
                                 "sbol20ToGenBank": True,
                                 "genBankToSbol20": False,
                                 "diff": False,
                                 "noncompliantUrisAllowed": False,
                                 "incompleteDocumentsAllowed": False,
                                 "bestPracticesCheck": False,
                                 "failOnFirstError": False,
                                 "displayFullErrorStackTrace": False,
                                 "ComponentDefinitionUri": "",
                                 "uriPrefix": "",
                                 "version": "",
                                 },
           "wantFileBack" : False}

request["mainFile"] = base64.b64encode(bytes(open("sequence1.xml").read(), "UTF-8")).decode("UTF-8")


resp = requests.post("http://www.async.ece.utah.edu/sbol-validator/endpoint.php", json=request)
```
This Python example prepares a JSON object, adds the base64-encoded string of the SBOL file, and POSTs the request to the specified endpoint.

### Browser Usage
To connect to the browser application, simply open a browser and navigate to the root directory. Upload a file for validation and select the desired options.
