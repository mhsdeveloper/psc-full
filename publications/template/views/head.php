<?php
/**
 * The html opening and head tag.

 */

	//website cache buster
	$FRONTEND_VERSION = 1926;

?>
	<?php 

		$PROJECTS_JSON = file_get_contents(SERVER_WWW_ROOT . "projects.json");

		$uri = $_SERVER['REQUEST_URI'];
		if(strpos($uri, '/publications/') !== false) $uri = explode('/publications',$uri)[1];
		$projectShortName = preg_replace("#(/)([^/]+)/(.*)#", "$2", $uri);

		$projects = json_decode($PROJECTS_JSON, true);
		if(!isset($projects['nameToID'][$projectShortName])) $projectShortName = "coop";

		if(!defined("PROJECT_SHORT_NAME")) define("PROJECT_SHORT_NAME", $projectShortName);

		if(isset($WORDPRESS)) $autorunMetaCI = "true";
		else $autorunMetaCI = "false";
?>

	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Source+Serif+Pro:ital,wght@0,400;0,600;1,400;1,600&family=Roboto:ital,wght@0,100;0,400;0,700;1,100;1,400;1,700&display=swap" rel="stylesheet">
	<title><?=$projects[$projects['nameToID'][$projectShortName]]['name'];?></title>
	<? /* these should be last, to allow to override WP theme */?>
	<script src="/publications/template/js/common.js?v=<?=$FRONTEND_VERSION;?>"></script>
	<script src="/publications/template/js/layouter.js?v=<?=$FRONTEND_VERSION;?>"></script>
	<script src="/publications/template/js/coophelpers.js?v=<?=$FRONTEND_VERSION;?>"></script>
	<script src="/publications/template/js/metaci.js?v=<?=$FRONTEND_VERSION;?>"></script>
	<script src="/publications/template/js/metadis.js?v=<?=$FRONTEND_VERSION;?>"></script>
	<? // see templates/views/head.php for more env setup ?>
	<script>
		const Projects = <?=$PROJECTS_JSON;?>

		const Env = {
			projectShortname: "<? echo PROJECT_SHORT_NAME;?>",
			apiURL: "/mhs-api/u1/",
			apiExtURL: "/mhs-api/ext/",
			searchAPIURL: "/publications/<? echo PROJECT_SHORT_NAME;?>/searchQuery"
		}
		Env.projectID = Projects.nameToID[Env.projectShortname];

		const METADIS = new MetaDis();
		const METACI = new MetaCI(METADIS, <?=$autorunMetaCI;?>); 
		document.getElementsByTagName("html")[0].classList.add(Env.projectShortname);

	</script>
	<!-- Google tag (gtag.js) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=<? echo GA_ACCOUNT_NO;?>"></script>
	<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', "<? echo GA_ACCOUNT_NO;?>");
	</script>
	<link href="/publications/template/css/theme1/global.css?v=<?=$FRONTEND_VERSION;?>" rel="stylesheet"/>
	<link href="/projects/<? echo PROJECT_SHORT_NAME;?>/customize/style.css" rel="stylesheet"/>
	<script src="/projects/<? echo PROJECT_SHORT_NAME;?>/customize/custom.js?v=<?=$FRONTEND_VERSION;?>"></script>
	<script src="/publications/template/js/yodude.js?v=<?=$FRONTEND_VERSION;?>"></script>
	<script src="/publications/template/js/name-lookup.js?v=<?=$FRONTEND_VERSION;?>"></script>
	<script src="/publications/template/js/datepicker.js?v=<?=$FRONTEND_VERSION;?>"></script>
