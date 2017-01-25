#!/bin/bash
chmod a+x scripts/*.sh

echo "#### BEGIN check-docker-install.sh ####"
./scripts/check-docker-install.sh
echo "#### END check-docker-install.sh ####"

echo ""

echo "#### BEGIN docker-compose-preflight.sh ####"
./scripts/docker-compose-preflight.sh
echo "#### END docker-compose-preflight.sh ####"

echo ""

echo "#### BEGIN is-docker-compose-running ####"
./scripts/is-docker-compose-running.sh
echo "#### END is-docker-compose-running ####"

echo ""

echo "#### BEGIN bootstrap-elasticsearch.sh ####"
./scripts/bootstrap-elasticsearch.sh
echo "#### END bootstrap-elasticsearch.sh ####"

echo ""

echo "#### BEGIN laravel-preflight.sh ####"
./scripts/laravel-preflight.sh
echo "#### END laravel-preflight.sh ####"

echo ""

echo "#### BEGIN updating laravel app ####"
./scripts/update-laravel.sh
echo "#### END updating laravel app ####"

echo ""

echo "install.sh finished..."