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

def check_deployment(deployment):
    print(deployment)
    form_url = deployment["base"] + deployment["form"]
    api_url = deployment["base"] + deployment["api"]

    form_request = requests.get(form_url)
    api_request = requests.get(api_url)

    if form_request.status_code != 200:
        raise ValueError

    if api_request.status_code != 405:
        raise ValueError


def check_deployments():
    deployments_file = os.path.join(os.path.abspath(os.path.dirname(__file__)), 'deployments.json')
    deployments_json = open(deployments_file, 'r').read()
    deployments = json.loads(deployments_json)

    for deployment in deployments:
        check_deployment(deployment)

