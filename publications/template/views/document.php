<?php ?><!DOCTYPE html>
<html lang="en">
<head>
<?php
	require_once(SERVER_WWW_ROOT . "html/publications/template/views/head.php");
?>
	<script src="/publications/template/js/persrefhunter.js?v=<?=$FRONTEND_VERSION;?>"></script>
	<script src="/publications/template/js/highlighter.js?v=<?=$FRONTEND_VERSION;?>"></script>
	<script src="/solr/solr-direct.js?v=<?=$FRONTEND_VERSION;?>"></script>
	<link rel="stylesheet" href="/publications/template/css/theme1/tei.css?v=<?=$FRONTEND_VERSION;?>" />
</head>
<body class="innerPage <?=$bodyClasses;?> document">

	<?php include(\MHS\Env::INCLUDES_PATH . "header.html");?>
	<article class="owrapper">

		<div class="" id="documentVue" v-scope @vue:mounted="mounted">

			<div class="nextPrev" v-scope="NextPrev({fields: []})" @vue:mounted="mounted"></div>

			<div class="col1">
				<div class="docContext" v-cloak>

					<div v-scope="AuthorRecipient(config)" @vue:mounted="mounted"></div>
					<div v-scope="TimePeriods(config)" @vue:mounted="mounted"></div>
					<div v-scope="PageImages({ pageImageMetadata })" @vue:mounted="mounted"></div>
				</div>
			</div>


			<div class="docSection">
				<div id="document" class="teiFragment"><?=$document;?></div>
			</div>

			<div class="col3">
				<div class="textSizing">
					<span tabindex="0" id="txtSizeSmaller" class="tsSmaller">A</span>
					<span tabindex="0" id="txtSizeLarger" class="tsLarger">A</span>
				</div>

				<div v-scope="SidebarContextualizer({})" @vue:mounted="mounted"></div>
			</div>

		</div>
	</article>

	<div class="metadataSection">
		<div class="mdWrapper">
			<div id="metadataTopics" class="metadataTopics"></div>
			<div id="metadata" class="metadata"></div>
		</div>
	</div>

	<?php include(SERVER_WWW_ROOT . "html/publications/template/views/component_author_recipient.html");?>
	<?php include(SERVER_WWW_ROOT . "html/publications/template/views/component_sidebar_contextualizer.html");?>
	<?php include(SERVER_WWW_ROOT . "html/publications/template/views/component_page_images.html");?>
	<?php include(SERVER_WWW_ROOT . "html/publications/template/views/component_nextprev.html");?>

	<?php include(SERVER_WWW_ROOT . "html/publications/template/views/component_time_period.html");?>
	<template id="timePeriods">
		<?php if(is_readable(\MHS\Env::APP_INSTALL_DIR . "support-files/timeperiods.xml")){ ?>
			<?php echo $this->controller->loadTimePeriods();?>
		<? };?>
	</template>




	<?php include(SERVER_WWW_ROOT . "html/publications/template/views/footer.php");?>

	<script src="/lib/openseadragon4.1/openseadragon.min.js"></script>
	<script src="/lib/dragster.js?v=<?=$FRONTEND_VERSION;?>"></script>
	<script type="module">
		import { createApp } from '/lib/petite-vue.es.js?v=<?=$FRONTEND_VERSION;?>';
		import { DocumentEnhancements} from "/publications/template/js/document-vue.js?v=<?=$FRONTEND_VERSION;?>";

		Env.searchAPIURL = "/publications/" + Env.projectShortname + "/searchDirect";

		DocumentEnhancements.config = {
			xpaths: {
				author: "//teiheader/filedesc/sourcedesc/bibl/author",
				recipient: "//teiheader/filedesc/sourcedesc/bibl/recipient",
				topics: "//teiheader/profiledesc/textclass/keywords[1]/list[1]/item",
				date: "//teiheader/filedesc/sourcedesc/bibl/date"
			},

			postMount(app){
				app.moveTopics(document.getElementById("metadataTopics"));
				let placementEl = document.getElementById("metadata");
				app.moveDocback(placementEl);
				app.moveInsertions(placementEl);
			}
		}

		DocumentEnhancements.pageImageMetadata = "";

		const App = createApp(DocumentEnhancements);
		App.mount("#documentVue");
	</script>

</body>
</html>
