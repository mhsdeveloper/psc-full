<?php ?><!DOCTYPE html>
<html lang="en">
<head>
<?php
	require_once("head.php");
?>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
	<link rel="stylesheet" href="/publications/template/css/theme1/tei.css?v=<?=$FRONTEND_VERSION;?>" />
</head>
<body class="innerPage searchTypePage readPage">
	<?php include(\MHS\Env::INCLUDES_PATH . "header.html");?>
	
	<div>
		<h1 class="title">Read</h1>
	</div>

	<?php include("searchCore.php");?>

	<?php include("footer.php");?>

	<script src="/solr/solr.js?v=<?=$FRONTEND_VERSION;?>"></script>
	<script type="module">
		import Search from "/publications/template/js/search-app-v3.js?v=<?=$FRONTEND_VERSION;?>";
		import { createApp } from '/lib/vue3.2.41-dev.js?v=<?=$FRONTEND_VERSION;?>';

		//call our special function to build the data function that Vue3 expects
		Search.buildData({
			facetFields: [
				{param: 'p', field: "person_keyword"},
				{param: 's', field: "subject"},
				{param: 'y', field: "date_year"}
			],
			whitelists: {
			}
		}, {
			mode: 'read'
		});

		const SearchApp = createApp(Search);
		SearchApp.mount("#searchApp");
	</script>

</body>
</html>
