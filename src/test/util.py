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

