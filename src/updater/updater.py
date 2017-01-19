from os.path import dirname, abspath
import subprocess

def update():
    project_dir = dirname(dirname(abspath(__file__)))
    subprocess.check_call(["/usr/bin/git", "pull"], shell=True, cwd=project_dir)


    if subprocess.call(["/bin/systemctl", "restart", "sbol-validator"], shell=True):
        return "Update successful"
    else:
        return "Update unsuccessful. Check the logs."
