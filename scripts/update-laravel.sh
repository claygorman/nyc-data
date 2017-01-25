#!/bin/bash
docker exec -it php.local bash -c "cd /usr/share/nginx && chmod a+x composer.phar && /usr/local/bin/php composer.phar update"