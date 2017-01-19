#!/bin/bash
exec 3>&1 4>&2 > update.log 2>&1

/usr/bin/git pull
/usr/bin/systemctl restart sbol-validator

exec 1>&3 2>&4