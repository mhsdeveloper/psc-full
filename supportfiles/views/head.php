<?php

	$FRONTEND_VERSION = 1;


?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><? echo \MHS\Env::PROJECT_HTML_TITLE;?></title>
<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons" rel="stylesheet" type="text/css">
<link href="/lib/quasar.2.12.4.prod.css" rel="stylesheet" type="text/css">
 <link rel="stylesheet" href="<? echo PATH_ABOVE_WEBROOT;?>css/docmanager.css?v=<?=$FRONTEND_VERSION;?>"/>
<script src="<? echo PATH_ABOVE_WEBROOT;?>js/preliminaries.js?v=<?=$FRONTEND_VERSION;?>"></script>
<script src="/lib/yodude.js?v=<?=$FRONTEND_VERSION;?>"></script>
<script src="<? echo PATH_ABOVE_WEBROOT_TO_SOLR_JS;?>?v=<?=$FRONTEND_VERSION;?>"></script>
