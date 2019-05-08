#!/usr/bin/env bash

cd /opt/SBOL-Validator/src
export FLASK_APP=validationapi.api

nginx &

rm -rf work
mkdir work

uwsgi --ini sbol-validator.ini


