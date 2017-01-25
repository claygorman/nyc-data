#!/bin/bash
echo "This scripts checks the existence of the docker compose env files."
echo "Checking..."
envfiles=( "elasticsearch-variables.env" "logstash-variables.env" "php-variables.env" "web-variables.env" )
for i in "${envfiles[@]}"
do
	if [ -f $(pwd)/vars/$i ]; then
		echo "$i exists..."
	else
	    echo "$i does not exist so we will make it for you... please set any env vars"
	    cp $(pwd)/vars/$i.example $(pwd)/vars/$i
	fi
done
echo "...done."