import subprocess

def update():
    command = "./update.sh"
    subprocess.call(command, universal_newlines=True, stderr=subprocess.STDOUT)