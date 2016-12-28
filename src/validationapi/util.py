from validator.validator import ValidationOptions, ValidationResult, ValidationRun
import uuid
import os


def do_validation(json):
    """
    Performs validation based on a json request
    """
    options = ValidationOptions(json['return_file'])
    options.build(json['options'])

    main_filename = os.path.join('work', str(uuid.uuid4()) + ".sbol")
    with open(main_filename, 'a+') as file:
        file.write(json["main_file"])

    if json['options']['test_equality']:
        diff_filename = os.path.join('work', str(uuid.uuid4()) + ".sbol")

        with open(diff_filename, 'a+') as file:
            file.write(json["diff_file"])

        run = ValidationRun(options, main_filename, diff_filename)
    else:
        run = ValidationRun(options, main_filename)

    result = run.execute()
    return result
