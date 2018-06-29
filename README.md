# SBOL Validator
[![Build Status](https://travis-ci.org/SynBioDex/SBOL-Validator.svg?branch=master)](https://travis-ci.org/SynBioDex/SBOL-Validator)  
A web-based validator for SBOL files backed by libSBOLj's validation runtimes. This validator offers support for SBOL2, SBOL1.1, and GenBank.
Furthermore, it is accessible through a web GUI or a RESTful API. 

The bleeding-edge version of the validator can be found [here](http://www.async.ece.utah.edu/sbol-validator/) with an API endpoint found at `http://www.async.ece.utah.edu/validate/`.

### Installation
First, bit about the way the application is structured.
There are two main parts:
 - The web frontend -- static HTML, CSS, and JavaScript files served by a webserver (nginx setup instructions below)
 - The REST API backend -- a Python Flask application served by uwsgi.

#### Prerequisites
You should have Git installed. To do that, execute the appropriate command for your system.

| OS | Command |
| --- | --- |
| Ubuntu | `sudo apt-get install git` |
| Mac | `brew install git` |

First, clone the repository into its own folder on your computer. To do so, navigate to the directory where you'd like to place this folder and run the command `git clone https://www.github.com/SynBioDex/SBOL-Validator.git` to create a new directory containing a clone of the validator. The setup is now ready to begin. 

#### Setting up the backend
##### Prerequisites
Install all of the necessary packages onto your system with the appropriate command for your system.

| OS | Command |
| --- | --- |
| Ubuntu | `sudo apt-get install python3 python3-pip` |
| Mac | `brew install python3 pip3 pyvenv` |

##### Set up virtual environment
Create a virtual environment for the validator in the parent directory. You can use the example commands below to create a virtualenv called `sbol-validator-venv`.

| OS | Command |
| --- | --- |
| Ubuntu | `virtualenv sbol-validator-venv` |
| Mac | `pyvenv sbol-validator-venv` |

Activate this virtualenv using the command `source sbol-validator-venv/bin/activate` (note: for the remainder of this guide, it will be assumed that your virtualenv is named `sbol-validator-venv` -- if it isn't, you'll have to use common sense to find out where to substitute the name you used.) You should see `(sbol-validator-venv)` before your command prompt if you've correctly activated the virtualenv. 

Navigate into the `SBOL-Validator` directory. Run the command `pip install -r requirements.txt` to install all of the necessary Python packages for the validator. 

This should be all that you need to run the backend manually. To test this, set the environment variable `FLASK_APP` to `validationapi.api` and run the command `python -m flask run` from within the `.../SBOL-Validator/src` directory. You should see the following message:

```
Unable to import uWSGI application.
This probably means that you're running the application manually.
If you are, ignore this message.
 * Serving Flask app "validationapi.api"
 * Running on http://127.0.0.1:5000/ (Press CTRL+C to quit)
```

##### Set up Systemd service
To ensure that the application is managed by the system and restarted if it crashes or if the system powers off, we will create a Systemd unit file.

Create a `systemd` unit file called `sbol-validator.service` as root user in `/etc/systemd/system`. In it, paste the following content: 

```
[Unit]
Description=uWSGI instance to serve SBOL Validator
After=network.target

[Service]
User=nginx
Group=nginx
WorkingDirectory=/PATH/TO/YOUR/VALIDATOR/SBOL-Validator/src
Environment="PATH=/PATH/TO/YOUR/VALIDATOR/sbol-validator-venv/bin"
ExecStart=/PATH/TO/YOUR/VALIDATOR/sbol-validator-venv/bin/uwsgi --ini sbol-validator.ini

[Install]
WantedBy=multi-user.target
```

Be sure to replace `/PATH/TO/YOUR/VALIDATOR/` with the actual path to your validator.

Ensure that the configuration is correct with `sudo systemctl restart sbol-validator`. If you see no errors, the validation service is up and running! To make sure that it runs at startup, run `sudo systemctl enable sbol-validator`. 

#### Setting up the frontend
##### Prerequisites
First, install `nginx`. Nginx will serve the static files for the frontend and direct any requests for the backend to uWSGI.

| OS | Command |
| --- | --- |
| Ubuntu | `sudo apt-get install nginx` |
| Mac | `brew install nginx` |

##### Create nginx configuration
Navigate to the `/etc/nginx/sites-available/` directory and create the file `sbol-validator.conf`.

Copy the following into the file:

```
server {
    listen 80;
    server_name server_domain_or_IP;
   
    location /sbol-validator/ {
        alias /PATH/TO/YOUR/VALIDATOR/SBOL-Validator/src/validation-form/;
        index html/form.html;
    }
    
    location /validate/ {
        include uwsgi_params;
        uwsgi_pass unix:/PATH/TO/YOUR/VALIDATOR/SBOL-Validator/src/sbol-validator.sock;
    }
    
    location /update/ {
        include uwsgi_params;
        uwsgi_pass unix:/PATH/TO/YOUR/VALIDATOR/SBOL-Validator/src/sbol-validator.sock;
    }
}
```

Once you've saved this, check that there are no typos with the command `sudo nginx -t`. If everything is correct, restart nginx with `sudo service nginx restart`.

With that, your validator should be online!


### API
The API documentation can be found [here](http://synbiodex.github.io/SBOL-Validator)
