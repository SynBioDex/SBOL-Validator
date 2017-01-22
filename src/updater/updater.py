from os.path import dirname, abspath
import subprocess
import uwsgi

def update():
    project_dir = dirname(dirname(abspath(__file__)))

    if not subprocess.check_output(["/usr/bin/git pull"], shell=True, cwd=project_dir):
        return "Update unsuccessful. Check the logs."

    uwsgi.reload()

    return "Update successful."
