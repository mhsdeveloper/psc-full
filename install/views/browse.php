<?php ?><!DOCTYPE html>
<html lang="en">
<head>
<?php
	require_once("head.php");
?>
<script src="/lib/vue2.6.12-debug.js"></script>
<script src="/publications/template/js/browse-app.js"></script>
<script src="/solr/solr.js"></script>
</head>
<body class="innerPage browse">
	<?php include("header.php");?>
	<div class="wrapper">
		<h1>Browse</h1>
		<article>
			<div id="browseApp">
				<div class="columns">
					<div class="facetList personKeyword column33">
						<h2>Most frequent names</h2>
						<a v-for="person in facets.person_keyword" class="personDocumentSearch" 
						   v-bind:href="'<? echo \MHS\Env::APP_INSTALL_URL?>find#0/20/p/' + person.name_key"
						>
							<div v-if="person.count > 0">
								<span class="name"><?php include(\MHS\Env::APP_INSTALL_DIR . "customize/name-template.html");?></span>
								<span class="counts">{{person.count}} document{{person.count > 1 ? "s" : ""}}</span>
							</div>
						</a>
					</div>

					<div class="facetList subjects column33">
						<h2>Most frequent subjects</h2>
						<a v-for="subject in facets.subject" class="subjectDocumentSearch"
							v-bind:href="'<? echo \MHS\Env::APP_INSTALL_URL?>find#0/20/s/' + encodeURIComponent(subject.name)"
						>
							<div v-if="subject.count > 0">
								<span class="name">{{subject.name}}</span>
								<span class="counts">{{subject.count}} document{{subject.count > 1 ? "s" : ""}}</span>
							</div>
						</a>
					</div>

					<div class="facetList years column33">
						<h2>Most frequent years</h2>
						<a v-for="year in facets.date_year" class="dateDocumentSearch" href="#" v-on:click="dateDocumentSearch(year.name)"
						>
							<div v-if="year.count > 0">
								<span class="name">{{year.name}}</span>
								<span class="counts">{{year.count}} document{{year.count > 1 ? "s" : ""}}</span>
							</div>
						</a>
					</div>

				</div>
			</div>
		</article>
	</div>

	<?php include("footer.php");?>
</body>
</html>
