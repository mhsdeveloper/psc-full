# Server Setup and Coop Install

## Prerequisites

The setup assumes you are familiar with the Linux command line, as well as with the MYSQL command line admin tool. Also, that you have root access to your server and the server is not running other web services. For example, make sure Apache is not installed. You can remove it on Debian systems with:
	
	sudo apt remove apache2

This guide takes you through each of the steps and shows the necessary terminal commands, hopefully providing all the necessary context. If you follow the setup as described here, everything should go well. The choice was made to do this instead of create an install script for several reasons: 1) creating an install script is tricky and needs to be maintained; 2) running through all the steps makes it clear what is going on, so that individual steps can be adjusted or adapted; 3) following from reason 2, if software or server environments change, having the steps laid out (rather than hidden behind a script and it's UI) will make future adjustments easier.

The installation process involves the following main steps:

1. Installing services that are provided by the OS distribution
2. Downloading the Coop Publications Software
3. Downloading and installing runtime software applications: SOLR, Wordpress
4. Configuring the various software to work together.


## Server Requirements

Linux server. The install instructions were tested with the following specs, but others may also work.

* 4 core CPU, 4GB ram, and 80GB disk
* Ubuntu Server 24.04 LTS
* nginx (version that ships with Ubuntu)
* PHP 8.3 fpm (version that ships with Ubuntu)
* MYSQL (version that ships with Ubuntu)
* Open JDK (version that ships with Ubuntu)

As you can see, really all you need is Ubuntu 24.04 LTS, and the rest are all provided by the distribution.


## 1: Install services provided by the OS distribution

Login to your server using your main login, especially a login you use for managing website files. Needless to say, do not login as root directly.

Use your system's package manager to install nginx, PHP fpm, MYSQL, and Open JDK. These are the versions the PSC has tested and uses on its server, you may be able to use newer versions with little or no change. On Ubuntu, the commands are as follows:

	sudo apt install nginx
	sudo apt install php8.3-fpm
	sudo apt install php8.3-mysqli
	sudo apt install php8.3-xsl
	sudo apt install php8.3-mbstring
	sudo apt install php8.3-gd
	sudo apt install php8.3-zip
	sudo apt install php8.3-xml
	sudo apt install php8.3-curl
	sudo apt install mysql-server
	sudo apt install default-jdk
	sudo apt install unzip
	sudo apt install nano

Some notes: the default-jdk is the GNU version of JAVA, and is required by SOLR. Unzip will be needed for the Solr install process. We also added nano to the list, since it's handy to have a better command line text editor.



## Install the Coop Software Package

The best way to get the software is to use git from the command line; you can checkout our software repository and keep your software up-to-date. 

Our github repository is at: https://github.com/mhsdeveloper/psc-full

You need to know your developer username; type "whoami" to see it. 

### To Use git:

1. use the command to install git:

	```
    sudo apt install git
	```

2. create the proper directory structure and ownership/permissions; change your-user-name-here to reflect your actual username:

	```
    sudo mkdir /psc
    sudo mkdir /psc/www
    sudo mkdir /psc/www/html
    sudo chown -R your-user-name-here /psc
    sudo chgrp -R your-user-name-here /psc
	sudo chmod -R g+w /psc
	```

3. We'll add the various web-servers to your user group. Nginx and PHP-fpm both run as the user www-data, so we add them to your user group:

	```
    sudo usermod -a -G your-user-name-here www-data
	```

4. Download the software: change to the correct directory, setup git, and then checkout our package with these commands:

	```
    cd /psc/www/html
    git init
	git branch -m main
    git checkout git@github.com/mhsdeveloper/psc-full.git
	```


## SOLR

### Download and install

Our setup was tested with Solr version 8.11.3. It is not the latest, but it is the most stable version of the 8 series. Download Apache SOLR from [SOLR's website](https://solr.apache.org/download) The procedure is to download the zip file to our install folder; then unpack a script "bin/install_solr_service.sh" from within the zip file, and use that script to unpack the rest of the archive and install. For convenience we've included the install script for 8.11.3, so you can just use the following commands to download and install:

	cd /psc/www/html/install
	wget --trust-server-names https://www.apache.org/dyn/closer.lua/lucene/solr/8.11.3/solr-8.11.3.zip?action=download
	sudo bash ./install_solr_service.sh solr-8.11.3.zip

If Solr starts but fails it's ok, we're not done yet; just press CTRL-C and continue with these instructions.

For reference and for different installation needs, read SOLR's documentation for installing as a service; see their page "Taking Solr to Production".

### Configure SOLR

Set the password for the solr user; choose a password and keep it somewhere safe:

	sudo passwd solr

Create the initial solr storage collection:

	sudo whoami
	su solr -c "/opt/solr/bin/solr create -c publications"

If asked, entered your main password for the sudo whoami command. Use the solr user password for the second command. For serious Linux users: we run the sudo whoami command to make sure the installer has recently "sudo'd". This effectively separates the two passwords, which can otherwise be very confusing.

Solr stores its config, index, and other data in /var/solr/data/publications/. In that folder, we need to remove the managed-schema and copy over our own schema for better performance:

	sudo rm /var/solr/data/publications/conf/managed-schema
	sudo cp /psc/www/html/install/server-configs/schema.xml /var/solr/data/publications/conf/

Adjust solrconfig.xml:
search for "<updateRequestProcessorChain" and see that in this line, autoCreateFields is false:
	<updateRequestProcessorChain name="add-unknown-fields-to-the-schema" default="${update.autoCreateFields:false}" ...

Remove any ManagedIndexSchemaFactory definition if it exists.

Add a ClassicIndexSchemaFactory definition:  
	​<schemaFactory class="ClassicIndexSchemaFactory"/>

### Restart SOLR

	sudo service solr restart

IMPORTANT SECURITY NOTE, FIREWALL SETUP: you should make sure your host/network is blocking external access to port 8983, which SOLR uses. That way, only the server itself (PHP) can access SOLR directly, but the outside world can not. It is also possible to configure your OS to block that port if your host ISP does not offer this service.





## Install Wordpress

### Download Wordpress

We've included the most recent version of Wordpress to be tested with the Coop software.

### Install the Coop theme and plugin

	-adjust wp-config.php : make sure server address and mysql user info are correct.







## Final Setup steps

Lastly, we need to configure all the parts of the system.


### MYSQL

Run the mysql install script and follow our recommended answers to the install questions:

	sudo mysql_secure_installation

Answer the question as follows:

	Validate password component: No
	Remove anonymous user: Yes
	Disallow remote root login: Yes
	Remove test database: Yes
	Reload privileges: Yes





	configuration.sh

		move psc wp theme from install into wp-content/themes
		add php configs to end of php.ini





	mysqldump and import these dbs: psccore, docmanager, pscfront

	create user
	grant all privileges on psccore.* to user@localhost;
	grant all privileges on pscfront.* to user@localhost;
	grant all privileges on docmanager.* to user@localhost;
	flush privileges;



configure /etc/nginx/nginx.conf
configure /etc/nginx/sites-available/wpmu.conf

Edit .env file for Laravel in html/mhs-api/.env

Edit the server-env.php file

set php.ini settings:
	auto_prepend_file = [path to]server-env.php
	The server-env.php file is at the root level of the main Coop package. It is recommended to place the server-env.php outside of the html directory.




