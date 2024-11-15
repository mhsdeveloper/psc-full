#!/bin/bash



# domainname='TESTDOMAIN'

# multisite="define('FS_METHOD', 'direct');
# define('MULTISITE', true);
# define('DOMAIN_CURRENT_SITE', '$domainname');
# define('PATH_CURRENT_SITE', '/');
# define('SITE_ID_CURRENT_SITE', 1);
# define('BLOG_ID_CURRENT_SITE', 1);
# define( 'WP_AUTO_UPDATE_CORE', false );
# "


# sed -i "s|/* That's all, stop editing! Happy publishing. */|$multisite|g" ../server-configs.php

# exit






echo "";

echo "This configuration script helps configure MYSQL, Wordpress, and"
echo "settings for the PSC Coop Server software."
echo "To use this successfully, you will need to know your site's domain name."

echo "";

scriptuser=`whoami`

echo "First confirm $scriptuser password."

#we do this so that 'sudo mysql' doesn't ask for a password right after we just had user type in the  mysql user password. CONFUSING!!
sudo echo " -- confirmed --"



echo ""
echo "Copying server config files to required locations..."
echo ""

sudo cp /psc/www/html/install/server-configs/server-env.php /psc/www/
sudo cp /psc/www/html/install/server-configs/environment.php /psc/www/
sudo cp /psc/www/html/install/server-configs/apikeys.php /psc/www/
sudo cp /psc/www/html/install/server-configs/mhs-api.env /psc/www/html/mhs-api/.env
sudo mkdir /psc/www/html/projects
sudo cp -r /psc/www/html/install/projects/coop /psc/www/html/projects/
sudo cp /psc/www/html/install/scripts/configure-wp-step2.php /psc/www/html/

echo ""
echo "Updating file permissions..."
echo ""

sudo chmod -R g+w /psc/www/html



echo "This will setup the initial database for the historical names manager and the document manager."

read -p "Pick a name for your mysql user: " user

read -p "Enter a password for your mysql user: " password
read -p "Confirm the password: " password2

if [ "$password" != "$password2" ]; then

	echo "Sorry, passwords did not match; please run the script again."
	echo ""
	exit

fi

read -p "What is the name of your organization? Enter the full name: " coopname
read -p "What is the domain name for your coop website? Begin with www: " domainname
read -p "If you are using Google Analytics, enter the Google ID, or leave blank to not use GA: " gano

echo ""
echo "Here is the info you entered: "
echo ""
echo "Mysql database user: $user"
echo "Mysql user password: $password"
echo "Organization name: $coopname"
echo "Website domain name: $domainname"
echo "Google Analytics ID: $gano"

echo ""
read -n1 -rsp "If you wish to change anything, press CTRL-C to abort and run the script again. Press anything else to continue ... " key
echo ""



echo "Creating databases and permissions..."

sudo mysql -e "CREATE USER $user@localhost"
sudo mysql -e "ALTER USER $user@localhost identified by '$password'"

sudo mysql -e "CREATE DATABASE frontend; CREATE DATABASE psccore; CREATE DATABASE docmanager;"

sudo mysql -e "GRANT ALL PRIVILEGES ON psccore.* TO $user@localhost;"
sudo mysql -e "GRANT ALL PRIVILEGES ON docmanager.* TO $user@localhost;"
sudo mysql -e "GRANT ALL PRIVILEGES ON frontend.* TO $user@localhost;"
sudo mysql -e "flush privileges;"


sudo mysql psccore < /psc/www/html/install/scripts/psccore-start.sql
sudo mysql docmanager < /psc/www/html/install/scripts/docmanager-start.sql


echo ""
echo "Adjusting server-env.php file..."
echo ""

sudo sed -i "s|\[\[EDIT-THIS-MYSQL-USER\]\]|$user|g" /psc/www/server-env.php
sudo sed -i "s|\[\[EDIT-THIS-MYSQL-PASSWORD\]\]|$password|g" /psc/www/server-env.php
sudo sed -i "s|\[\[EDIT-THIS-COOP-NAME\]\]|$coopname|g" /psc/www/server-env.php
sudo sed -i "s|\[\[EDIT-THIS-DOMAIN-NAME\]\]|$domainname|g" /psc/www/server-env.php
sudo sed -i "s|\[\[EDIT-THIS-DOMAIN-NAME\]\]|$domainname|g" /psc/www/html/configure-wp-step2.php
sudo sed -i "s|\[\[EDIT-THIS-GANO\]\]|$gano|g" /psc/www/server-env.php

echo ""
echo "Adjusting mhs-api .env file ..."
echo ""

sudo sed -i "s|\[\[EDIT-THIS-MYSQL-USER\]\]|$user|g" /psc/www/html/mhs-api/.env
sudo sed -i "s|\[\[EDIT-THIS-MYSQL-PASSWORD\]\]|$password|g" /psc/www/html/mhs-api/.env

echo ""
echo "Creating initial project and directories ..."
echo ""

sudo sed -i "s|\[\[EDIT-THIS-COOP-NAME\]\]|$coopname|g" /psc/www/html/projects/coop/environment.php
sudo sed -i "s|\[\[EDIT-THIS-DOMAIN-NAME\]\]|$domainname|g" /psc/www/html/projects/coop/environment.php
sudo sed -i "s|\[\[EDIT-THIS-COOP-NAME\]\]|$coopname|g" /psc/www/html/projects/coop/support-files/footer.html

echo ""
echo "All set!"
echo ""
echo "You can change many of these settings in the server-env.php file."
echo ""
