#!/bin/bash

# remove any existing or shipped site configs
sudo rm /etc/nginx/sites-enabled/*

sudo cp /psc/www/html/install/server-configs/wpmu.conf /etc/nginx/sites-available/

echo ""
echo "Configuring Nginx"
echo ""
read -p "Enter your website's domain name, without the leading www (e.g. mydomain.org): " domainname 
echo ""
read -p "Enter your php version (or blank for 8.3): " phpversion

if [ "$phpversion" = "" ]; then 
	phpversion="8.3"
fi

echo ""
echo "You entered $domainname for your website's domain name."
echo "You entered $phpversion for your php version."
echo ""
read -n1 -rsp "If you wish to change anything, press CTRL-C to abort and run the script again. Press anything else to continue ... " key


sudo sed -i "s|DOMAINNAMEORG|$domainname|g" /etc/nginx/sites-available/wpmu.conf

sudo sed -i "s|PHPVERSION|$phpversion|g"  /etc/nginx/sites-available/wpmu.conf


sudo ln -s /etc/nginx/sites-available/wpmu.conf /etc/nginx/sites-enabled/wpmu.conf
sudo cp /psc/www/html/install/server-configs/wpmu-nginx.conf /etc/nginx/conf.d/

echo ""
echo "Changes complete."
echo ""

