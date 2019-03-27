
FROM ubuntu:18.04

ENV LANG C.UTF-8
ENV LC_ALL C.UTF-8

RUN apt-get update


RUN apt-get install -y python3 python3-pip default-jdk

RUN pip3 install virtualenv


RUN mkdir /opt/SBOL-Validator && \
	cd /opt/SBOL-Validator && \
	virtualenv sbol-validator-venv

ADD requirements.txt /opt/SBOL-Validator/
ADD src /opt/SBOL-Validator/src

RUN cd /opt/SBOL-Validator && \
	. sbol-validator-venv/bin/activate && \
	pip install -r requirements.txt

RUN apt-get install -y nginx
ADD docker/nginx.conf /etc/nginx/nginx.conf

ADD docker/sbol-validator.ini /opt/SBOL-Validator/src/sbol-validator.ini

ADD docker/run.sh /opt/

CMD [ "/opt/run.sh" ]


