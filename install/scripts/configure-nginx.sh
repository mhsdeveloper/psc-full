#!/bin/bash

# remove any existing or shipped site configs
rm /etc/nginx/sites-enabled/*

cp ../server-configs/wpmu.conf /etc/nginx/sites-available/

echo ""
echo "Configuring Nginx"
echo ""
read -p "Enter your website's domain name, without the leading www (e.g. mydomain.org): " domainname 
echo ""
read -p "Enter your php version (or blank for 8.3): " phpversion

if [ "$phpversion" = "" ]; then 
	phpversion = "8.3"
fi

echo ""
echo "You entered $domainname for your website's domain name."
echo "You entere $phpversion for your php version."
echo ""
read -n1 -rsp "If you wish to change anything, press CTRL-C to abort and run the script again. Press anything else to continue ... " key


sed -i "s|DOMAINNAMEORG|$domainname|g" /etc/nginx/sites-available/wpmu.conf

sed -i "s|PHPVERSION|$phpversion|g"  /etc/nginx/sites-available/wpmu.conf


ln -s /etc/nginx/sites-available/wpmu.conf /etc/nginx/sites-enabled/wpmu.conf
cp ../server-configs/wpmu-nginx.conf /etc/nginx/conf.d/
