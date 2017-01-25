#!/bin/bash
echo "This scripts checks the existence of the docker compose env files."
echo "Checking..."
envfiles=( "elasticsearch-variables.env" "logstash-variables.env" "mysql-variables.env" "php-variables.env" "web-variables.env" )
for i in "${envfiles[@]}"
do
	if [ -f $(PWD)/vars/$i ]; then
		echo "$i exists..."
	else
	    echo "$i does not exist so we will make it for you... please set any env vars"
	    cp $(PWD)/vars/$i.example $(PWD)/vars/$i
	fi
done
echo "...done."