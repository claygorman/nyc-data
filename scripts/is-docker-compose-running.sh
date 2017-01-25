#!/bin/bash

# The script checks if a container is running.
#   OK - running
#   WARNING - container is ghosted
#   CRITICAL - container is stopped
#   UNKNOWN - does not exist

containers=( "elasticsearch.local" "php.local" "logstash.local" "nginx.local" "redis.local" )
for i in "${containers[@]}"
do
	CONTAINER=$i

	RUNNING=$(docker inspect --format="{{ .State.Running }}" $CONTAINER 2> /dev/null)

	if [ $? -eq 1 ]; then
	  echo "UNKNOWN - $CONTAINER does not exist."
	  exit 3
	fi

	if [ "$RUNNING" == "false" ]; then
	  echo "CRITICAL - $CONTAINER is not running."
	  exit 2
	fi

	STARTED=$(docker inspect --format="{{ .State.StartedAt }}" $CONTAINER)

	echo "OK - $CONTAINER is running. StartedAt: $STARTED"
done
echo "all containers are running..."
