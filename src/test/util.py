import json
import os
import requests
from validator.validator import ValidationOptions

def check_commands():
    test_cases_file = os.path.join(os.path.abspath(os.path.dirname(__file__)), 'test_cases.json')
    test_cases_json = open(test_cases_file, 'r').read()
    test_cases = json.loads(test_cases_json)

    for idx, test_case in enumerate(test_cases):
        if idx % 1000 == 0:
            print(str(idx) + " test cases checked.")

        options = ValidationOptions('return file')

        options.build(test_case['options'])

        if test_case["correct"]:
            generated_command = options.command('libSBOLj.jar', 'main.xml', 'diff.xml')
            generated_command[5] = 'out.xml'
            if sorted(generated_command) != sorted(test_case['command']):
                print(test_case)
                print(options.command('libSBOLj.jar', 'main.xml', 'diff.xml'))
                raise ValueError
        else:
            try:
                options.command('libSBOLj.jar', 'main.xml', 'diff.xml')
            except ValueError:
                continue

            raise ValueError

    print("All " + str(len(test_cases)) + " commands correct.")

def check_deployment(deployment, sbol):
    form_url = deployment["base"] + deployment["form"]
    api_url = deployment["base"] + deployment["api"]

    form_request = requests.get(form_url)
    api_request = requests.get(api_url)

    if form_request.status_code != 200:
        raise ValueError

    if api_request.status_code != 405:
        raise ValueError

    request = {'options': {'language' : 'GenBank',
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
               'main_file': sbol
              }


    resp = requests.post("https://apps.nonasoftware.org/validate/", json=request)

    if resp.status_code != 200:
        raise ValueError

    validation = resp.json()

    if not validation['valid']:
        raise ValueError

    if validation['check_equality']:
        raise ValueError

    if len(validation['errors']) != 1:
        raise ValueError

def check_deployments():
    deployments_file = os.path.join(os.path.abspath(os.path.dirname(__file__)), 'deployments.json')
    deployments_json = open(deployments_file, 'r').read()
    deployments = json.loads(deployments_json)

    sbol_file = os.path.join(os.path.abspath(os.path.dirname(__file__)), 'sequence1.xml')
    sbol = open(sbol_file, 'r').read()

    for deployment in deployments:
        check_deployment(deployment, sbol)

