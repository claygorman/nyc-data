## Description
This project shows, by default, a view of the top 10 thai restuarants inspected by the NY Health Department.

Places with less then a B rating were not included in the view.

I believe the top places are the ones with the highest ratings and the most inspections. 

More inspections means more high ratings.

## Requirements
* docker [install](https://docs.docker.com/engine/installation/)
* docker-compose [install](https://docs.docker.com/compose/install/)
* php (recommended see [notes](#notes)) (http://php.net/manual/en/install.php)

## Containers
* logstash [(logstash:5)](https://hub.docker.com/_/logstash/)
* elasticsearch [(elasticsearch:5.1.2)](https://hub.docker.com/_/elasticsearch/)
* redis [(redis:alpine)](https://hub.docker.com/_/redis/)
* nginx [(nginx:1.10.2)](https://hub.docker.com/_/redis/)
* php (extended locally php/Dockerfile) [(php:7.1-fpm)](https://hub.docker.com/_/php/)

## Installation
* Run the docker pre-requisite check `./scripts/check-docker-install.sh`
* Run the docker-compose preflight script `./scripts/docker-compose-preflight.sh`
* Edit the `vars/web-variables.env` with the hostname that nginx will be reachable at
* If you wish to change nginx to use a port other then 9090 on line 7 in `docker-compose.yml`
* (debian only) Elasticsearch recommends setting max_map_count on the host `sysctl -w vm.max_map_count=262144`
* Now we move onto the laravel web app `./scripts/laravel-preflight.sh`
* Edit the `www/.env` file for your web app

Suggested settings to pay close attention to in the `www/.env`:

```
APP_ENV=production
APP_KEY=..... (see commands section)
APP_DEBUG=false
GOOGLE_MAPS_API_KEY=... (see notes section)
```

## Run the containers
* Run `docker-compose up -d` (remove -d if you want to run in foreground)
* Make sure everything is running with helper `./scripts/is-docker-compose-running.sh`
* Once everything is up for a minute or so (docker ps) then move to next step
* Run the database seeder `./scripts/bootstrap-elasticsearch.sh` (took about 10-15 minutes or so on my macbook pro 2015)
* optional: you can stop the logstash container if you want after importing `docker-compose down logstash.local`
* Update laravel `./scripts/update-laravel.sh`

## Commands
Generate laravel app key:

`docker exec -it php.local bash -c "cd /usr/share/nginx && /usr/local/bin/php artisan key:generate"`


Reindex the data (takes about 15 minutes or so)

`./scripts/bootstrap-elasticsearch.sh`

## Notes
* You can pipe php commands through the php container if you want
* [Google Maps Api Key](https://developers.google.com/maps/documentation/javascript/get-api-key)
