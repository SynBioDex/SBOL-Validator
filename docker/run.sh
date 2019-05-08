#!/usr/bin/env bash

cd /opt/SBOL-Validator
source sbol-validator-venv/bin/activate
cd src
export FLASK_APP=validationapi.api

nginx &

rm -rf work
mkdir work

../sbol-validator-venv/bin/uwsgi --ini sbol-validator.ini


