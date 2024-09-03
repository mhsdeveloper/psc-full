#!/bin/bash

# remove any existing or shipped site configs
rm /etc/nginx/sites-enabled/*

cp ../server-configs/wpmu.conf /etc/nginx/sites-available/
ln -s /etc/nginx/sites-available/wpmu.conf /etc/nginx/sites-enabled/wpmu.conf
cp ../server-configs/wpmu-nginx.conf /etc/nginx/conf.d/
