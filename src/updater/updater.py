import git
from os.path import dirname, abspath
import subprocess

def update():
    project_dir = dirname(dirname(abspath(__file__)))
    print(project_dir)

    repo = git.cmd.Git(project_dir)
    repo.pull()

    # if subprocess.call("/bin/systemctl restart sbol-validator"):
    #     return "Update successful"
    # else:
    #     return "Update unsuccessful. Check the logs."
