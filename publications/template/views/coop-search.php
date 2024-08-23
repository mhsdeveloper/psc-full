<?php ?><!DOCTYPE html>
<html lang="en">
<head>
<?php
	require_once("head.php");
?>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
	<link rel="stylesheet" href="/publications/template/css/theme1/tei.css?v=<?=$FRONTEND_VERSION;?>" />
</head>
<body class="coop-page innerPage searchTypePage searchPage">
	<?php include(\MHS\Env::INCLUDES_PATH . "header.html");?>

	<div class="inner-container">
		<h1 class="title">Search All Editions</h1>
	</div>

	<?php include("searchCoreAdv.php");?>

	<?php include("footer.php");?>

	<script src="/solr/paginator.js?v=<?=$FRONTEND_VERSION;?>"></script>
	<script src="/solr/solr-direct.js?v=<?=$FRONTEND_VERSION;?>"></script>
	<script>
		let Searcher = new SolrDirect();
		Searcher.addFacetFields(["person_keyword", "subject"]);
	</script>
	<script type="module">

		import App from "/publications/template/js/search-app.js?v=<?=$FRONTEND_VERSION;?>";
		import { createApp } from '/lib/vue3.2.41-dev.js?v=<?=$FRONTEND_VERSION;?>';

		Env.projectID = 0;
//		Env.projectShortname = "projects";
		Env.searchAPIURL = "/publications/searchQuery";

		const SearchApp = createApp(App);
		SearchApp.mount("#searchApp");
	</script>

	<!-- <script src="/lib/feedback.js?v=<?=$FRONTEND_VERSION;?>"></script> -->
</body>
</html>
