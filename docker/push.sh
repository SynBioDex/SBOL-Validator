#!/bin/bash

docker login -u "$DOCKER_USERNAME" -p "$DOCKER_PASSWORD"
docker push myersresearchgroup/sbolvalidator:snapshot
docker push myersresearchgroup/sbolconverter:snapshot
