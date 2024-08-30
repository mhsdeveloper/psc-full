#!/bin/bash

cp ../server-configs/wpmu.conf /etc/nginx/sites-available/
ln -s /etc/nginx/sites-available/wpmu.conf /etc/nginx/sites-available/wpmu.conf
cp ../server-configs/wpmu-nginx.conf /etc/nginx/conf.d/
