#!/bin/bash

cd /psc/www/html/install/scripts

php ./wp-cli.phar plugin activate wordpress-importer

php ./wp-cli.phar --url=[[EDIT-THIS-DOMAIN-NAME]] import /psc/www/html/install/demo/main-site.xml

