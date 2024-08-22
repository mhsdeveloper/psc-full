<?php

	$test = php_sapi_name();
	if($test != "cli") die("This must be run from the command line.");


	//opting to not do these	exec("sudo apt install php8.3-fpmNONE");

	$line = readline("Command: ");
	print $line;

	//dump history
	$cmds = readline_list_history();
	readline_clear_history();
 



	//first get info: install path??

	//get name of user??




	//setup SOLR schema


	//add php ini settings


	$phpIniSettings =<<<EOF

;--PSC--SETTINGS

short_open_tag = On
max_execution_time = 60
max_input_time = 60
memory_limit = 128M
post_max_size = 40M
auto_prepend_file = /psc/www/server-env.php
include_path = ".:/psc/www/html/publications/mhs/"
upload_max_filesize = 20M
max_file_uploads = 20

EOF;