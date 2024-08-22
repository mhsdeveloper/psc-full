<?php

	$FRONTEND_VERSION = 171;


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
<script>
	let Env = {
		baseURL: "<? echo PATH_ABOVE_WEBROOT;?>",
		viewURL: "<? echo \MHS\Env::DOC_VIEW_URL_PREFIX;?>",
		apiURL: "/mhs-api/u1/", // PSC ONLY
		lumAPI: "/mhs-api/v1/", //PSC ONLY

		projectID: <? echo \MHS\Env::PROJECT_ID;?>,
		username:  "<?=$_SESSION['PSC_USER'];?>",
		role: "<?=$_SESSION['PSC_ROLE'];?>",
		level: <? echo \Customize\User::level();?>,
		projectShortname: "<? echo \MHS\Env::PROJECT_SHORTNAME;?>",

		//see also the projects \MHS\Env file for the path to the server-side settings.json
		solrSetup: {
			url: "<? echo PATH_ABOVE_WEBROOT;?>search",
			configURL: "",
			prevPageLabel: "prev",
			nextPageLabel: "next",
			rows: 1000,
			//this will tell solr.js to automatically update the pagination html
			paginationElement: document.getElementById("pagination"),
			trackHash: false
		},

		labeling: {
			file: "<? echo DM_FILE_NAME_LABEL;?>",
			filename: "Filename",
			checkedoutTo: "<? echo DM_CHECKEDOUT_TO;?>",
			
		},

		//other settings
		validateSchemaOnUpload: true

	}
</script>
<? include("./customize-frontend/head.php");?>