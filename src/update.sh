#!/bin/bash
exec 3>&1 4>&2 > update.log 2>&1

git pull
systemctl restart sbol-validator

exec 1>&3 2>&4