import itertools
import json
import os
import sys


names = [
"language",
"subset_uri",
"fail_on_first_error",
"provide_detailed_stack_trace",
"check_uri_compliance",
"check_completeness",
"check_best_practices",
"uri_prefix",
"version",
"insert_type",
"test_equality",
"return_file",
"main_file_name",
"diff_file_name",
]

problem_space = [
    ["SBOL2", "SBOL1", "GenBank", "FASTA"],
    [False, "subset uri"],
    [False, True],
    [False, True],
    [False, True],
    [False, True],
    [False, True],
    [False, "uri prefix"],
    [False, "version"],
    [False, True],
    [False, True],
    [False, True],
    ["main file"],
    ["comparison file"]
]

all_options = [dict(zip(names, x)) for x in itertools.product(*problem_space) if (x[2] == x[3]) or (x[2] and not x[3])]

test_cases = []

for options in all_options:
    if options['provide_detailed_stack_trace'] and not options['fail_on_first_error']:
        test_cases.append({'correct': False, 'command': [], 'options': options})
        continue

    command = ['/usr/bin/java', '-jar', 'libSBOLj.jar', 'main.xml', '-o', 'out.xml', '-l', options["language"]]

    if options['test_equality']:
        command += ["-e", 'diff.xml', "-mf", 'main file', "-cf", 'comparison file']

    if options['subset_uri']:
        command += ["-s", 'subset uri']

    if options['fail_on_first_error']:
        command += ["-f"]

    if options['provide_detailed_stack_trace']:
        command += ["-d"]

    if not options['check_uri_compliance']:
        command += ["-n"]

    if not options['check_completeness']:
        command += ["-i"]

    if options['check_best_practices']:
        command += ["-b"]

    if options['uri_prefix']:
        command += ["-p", 'uri prefix']

    if options['version']:
        command += ["-v", 'version']

    if options['insert_type']:
        command += ["-t"]

    test_cases.append({'correct': True, 'command': command, 'options': options})

open(os.path.join(os.path.abspath(os.path.dirname(__file__)), 'test_cases.json'), mode='w').write(json.dumps(test_cases))
