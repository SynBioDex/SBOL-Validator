#!/bin/bash

docker login -u "$DOCKER_USERNAME" -p "$DOCKER_PASSWORD"
docker push zachzundel/sbolvalidator:snapshot
docker push zachzundel/sbolconverter:snapshot
