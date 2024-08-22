<?php

	$FRONTEND_VERSION = 35;

	if(TEST_MODE){
		if(!isset($_GET['muck'])) die("Sorry, WETVAC is in testing mode; only those in the know can muck around.");
	}


?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Primary Source Coop - WETVAC</title>
<link rel="shortcut icon" href="/favicon.ico" />
