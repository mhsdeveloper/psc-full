#!/bin/bash

scriptuser=`whoami`

echo "First confirm $scriptuser password."

#we do this so that 'sudo mysql' doesn't ask for a password right after we just had user type in the  mysql user password. CONFUSING!!
sudo echo " -- confirmed --"

echo "This will setup the initial database for the historical names manager and the document manager."

read -p "Pick a name for your mysql user: " user

read -p "Enter a password for your mysql user: " password
read -p "Confirm the password: " password2

if [ "$password" != "$password2" ]; then

	echo "Sorry, passwords did not match; please run the script again.\n";
	exit

fi

echo "\nCreating databases and permissions...\n"

sudo mysql -e "CREATE USER $user@localhost"
sudo mysql -e "ALTER USER $user@localhost identified by '$password'"

sudo mysql -e "CREATE DATABASE frontend; CREATE DATABASE psccore; CREATE DATABASE docmanager;"

sudo mysql -e "GRANT ALL PRIVILEGES ON psccore.* TO $user@localhost;"
sudo mysql -e "GRANT ALL PRIVILEGES ON docmanager.* TO $user@localhost;"
sudo mysql -e "GRANT ALL PRIVILEGES ON frontend.* TO $user@localhost;"
sudo mysql -e "flush privileges;"


echo "\nAdjusting server-env.php file...\n"

sed -i "s_[[EDIT-THIS-MYSQL-USER]]_$user_g" /psc/www/server-env.php
sed -i "s_[[EDIT-THIS-MYSQL-PASSWORD]]_$password_g" /psc/www/server-env.php



echo "\nAll set!.\n\n"