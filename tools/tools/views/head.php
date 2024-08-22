<?php

	$FRONTEND_VERSION = 50;


?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><? echo \MHS\Env::PROJECT_HTML_TITLE;?></title>
<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons" rel="stylesheet" type="text/css">
<link href="https://cdn.jsdelivr.net/npm/quasar@1.15.11/dist/quasar.min.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<? echo $this->base_dir();?>/css/docmanager.css?v=<?=$FRONTEND_VERSION;?>"/>
<script src="/lib/yodude.js"></script>
<script src="/lib/vue2.6.12.js"></script>
   <script>
		var username = "<?=$_SESSION['PSC_USER'];?>";
		var role = "<?=$_SESSION['PSC_ROLE'];?>";
		var projectShortname = "<? echo \MHS\Env::PROJECT_SHORTNAME;?>";
	window.addEventListener("DOMContentLoaded", function(){
	});
</script>
