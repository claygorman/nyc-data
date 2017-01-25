#!/bin/bash
echo "set permissions on storage folder"
chmod -R 777 $(pwd)/www/storage
echo ""
echo "This scripts checks the existence of the laravel env file."
echo "Checking..."
if [ -f $(pwd)/www/.env ]; then
	echo "laravel .env exists."
else
	cp $(pwd)/www/.env.example $(pwd)/www/.env
fi
echo "...done."
