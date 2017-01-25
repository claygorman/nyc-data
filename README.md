import data once containers are up and wait for exit
took about 10 minutes on my macbook pro 2015
`cd logstash && ./import-data.sh`

you can stop the logstash container if you want after importing
`docker-compose down logstash.local`

in www
composer update
set your .env file 
npm install
gulp