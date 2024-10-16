<?php

//set any of these to someting other than false to block user from accessing the tools.
//set to a string to also display that as a message.
define("MAINTENANCE_NAMES_DB", false);
define("MAINTENANCE_TOPICS_DB", false);
define("MAINTENANCE_DOCMANAGER", false);
define("MAINTENANCE_TOOLS", false); //for wetvac and anything in html/tools



define("COOP_NAME", "[[EDIT-THIS-COOP-NAME]]");
define("SERVER_WWW_ROOT", "/psc/www/");
define("SERVER_HTML_ROOT", "/psc/www/html/");
define("PROJECTS_FOLDER", "projects");
define("PSC_PROJECTS_PATH", SERVER_HTML_ROOT . "/" . PROJECTS_FOLDER . "/");


//this is the user for all the databases
define("MYSQL_USER", "[[EDIT-THIS-MYSQL-USER]]");
define("MYSQL_USER_PASSWORD", "[[EDIT-THIS-MYSQL-PASSWORD]]");

//this is the id for your Google Analytics account
// make the acct # empty to skip using google Analytics
define("GA_ACCOUNT_NO", "[[EDIT-THIS-GANO]]");


//the domain name of your public site
define("COOP_LIVE_DOMAIN", "[[EDIT-THIS-DOMAIN-NAME]]");
//the ip address of your public site
define("COOP_LIVE_IP", "[[EDIT-THIS-IP]]");
//the ip address of an external testing site, if you use it
define("COOP_TEST_IP", "12.34.56.67");
//the ip address of a local testing virtual box or other server on your local PC
define("LOCAL_TEST_IP", "192.168.56.1");


//If Java is not in your system's path, specify it's path here
define("ALT_JAVA_PATH", "/usr/bin/java");

//set this to true if you only run a live site and don't need to test code on a testing server
// OR if you don't know what your IP is.
define("COOP_SINGLE_INSTALL", false);



