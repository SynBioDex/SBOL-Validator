from validator.validator import ValidationOptions, ValidationResult, ValidationRun
import uuid
import os


def do_validation(json):
    options = ValidationOptions()
    options.build(json['options'])

    main_filename = os.path.join('work', str(uuid.uuid4()) + ".sbol")
    with open(main_filename, 'a+') as f:
        f.write(json["main_file"].encode('utf-8'))
    
    if json['options']['test_equality']:
        diff_filename = os.path.join('work', str(uuid.uuid4()) + ".sbol")

        with open(diff_filename, 'a+') as f:
            f.write(json["diff_file"].encode("utf-8"))

        run = ValidationRun(options, main_filename, diff_filename)
    else:
        run = ValidationRun(options, main_filename)
    
    result = run.execute()
    return result
