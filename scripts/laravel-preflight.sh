#!/bin/bash
echo "This scripts checks the existence of the laravel env file."
echo "Checking..."
if [ -f $(PWD)/www/.env ]; then
	echo "laravel .env exists."
else
	cp $(PWD)/www/.env.example $(PWD)/www/.env
fi
echo "...done."