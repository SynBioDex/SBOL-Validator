from os.path import dirname, abspath
import subprocess

try:
    import uwsgi
except ImportError:
    print("Unable to import uWSGI application.")
    print("This probably means that you're running the application manually.")
    print("If you are, ignore this message.")

def update():
    project_dir = dirname(dirname(abspath(__file__)))

    if not subprocess.check_output(["/usr/bin/git pull"], shell=True, cwd=project_dir):
        return "Update unsuccessful. Check the logs."

    uwsgi.reload()

    return "Update successful."
