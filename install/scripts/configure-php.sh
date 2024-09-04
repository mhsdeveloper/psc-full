#!/bin/bash


echo 'short_open_tag = On 
max_execution_time = 60 
max_input_time = 60 
memory_limit = 128M 
post_max_size = 40M 
auto_prepend_file = /psc/www/server-env.php 
include_path = ".:/psc/www/html/publications/mhs/" 
upload_max_filesize = 20M 
max_file_uploads = 20 
' >> /etc/php/8.3/fpm/php.ini
