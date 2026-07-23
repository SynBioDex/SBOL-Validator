FROM python:3.9-alpine

RUN apk add nginx openjdk17-jre python3-dev build-base linux-headers pcre-dev bash

WORKDIR /opt/SBOL-Validator

COPY requirements.txt .
COPY src src
COPY docker/nginx-converter.conf /etc/nginx/nginx.conf
COPY docker/sbol-validator.ini /opt/SBOL-Validator/src/sbol-validator.ini
COPY docker/run.sh /opt/SBOL-Validator

RUN pip install --upgrade pip
RUN pip install -r requirements.txt
RUN chmod -R 777 .

CMD [ "/opt/SBOL-Validator/run.sh" ]


