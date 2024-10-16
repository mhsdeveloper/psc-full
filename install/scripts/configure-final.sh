#!/bin/bash

echo "";

echo "This configuration script helps configure MYSQL, Wordpress, and the"
echo "settings for the PSC Coop Server software."
echo "To use this successfully, you will need your site's domain name."

echo "";

scriptuser=`whoami`

echo "First confirm $scriptuser password."

#we do this so that 'sudo mysql' doesn't ask for a password right after we just had user type in the  mysql user password. CONFUSING!!
sudo echo " -- confirmed --"

echo "This will setup the initial database for the historical names manager and the document manager."

read -p "Pick a name for your mysql user: " user

read -p "Enter a password for your mysql user: " password
read -p "Confirm the password: " password2

if [ "$password" != "$password2" ]; then

	echo "Sorry, passwords did not match; please run the script again."
	echo ""
	exit

fi

echo "Creating databases and permissions..."

sudo mysql -e "CREATE USER $user@localhost"
sudo mysql -e "ALTER USER $user@localhost identified by '$password'"

sudo mysql -e "CREATE DATABASE frontend; CREATE DATABASE psccore; CREATE DATABASE docmanager;"

sudo mysql -e "GRANT ALL PRIVILEGES ON psccore.* TO $user@localhost;"
sudo mysql -e "GRANT ALL PRIVILEGES ON docmanager.* TO $user@localhost;"
sudo mysql -e "GRANT ALL PRIVILEGES ON frontend.* TO $user@localhost;"
sudo mysql -e "flush privileges;"


echo ""
echo "Adjusting server-env.php file..."
echo ""

sed -i "s_\[\[EDIT-THIS-MYSQL-USER\]\]_$user_g" /psc/www/server-env.php

exit

sed -i "s_[[EDIT-THIS-MYSQL-PASSWORD]]_$password_g" /psc/www/server-env.php

echo ""
echo "Adjusting wp-config.php file..."
echo ""

sed -i "s_[[EDIT-THIS-MYSQL-USER]]_$user_g" /psc/www/html/wp-config.php
sed -i "s_[[EDIT-THIS-MYSQL-PASSWORD]]_$password_g" /psc/www/html/wp-config.php


read -p "What is the name of your organization? Enter the full name: " coopname
read -p "What is the URL for your server? Begin with www: " domainname
read -p "If you are using Google Analytics, enter the Google ID, or leave blank to not use GA: " gano


sed -i "s_[[EDIT-THIS-COOP-NAME]]_$coopname_g" /psc/www/server-env.php
sed -i "s_[[EDIT-THIS-DOMAIN-NAME]]_$domainname_g" /psc/www/server-env.php
sed -i "s_[[EDIT-THIS-DOMAIN-NAME]]_$domainname_g" /psc/www/html/wp-config.php
sed -i "s_[[EDIT-THIS-GANO]]_$gano_g" /psc/www/server-env.php

echo "\nAll set!.\n"

echo "You can change many of these settings in the server-env.php file.\n\n";