<?php
	require_once("head.php");

	$temp = explode("/", $_SERVER['REQUEST_URI']);
	$HUSC = $temp[count($temp) -1];
?>
</head>
<body class="innerPage person">
	<?php include(\MHS\Env::INCLUDES_PATH . "header.html");?>

	<div class="darkFullBleed">
		<div class="iwrapper">
			<h2 class="title">Explore People
				<div class="lookup findPerson">
					<label>Find a person:</label>
					<div class="nameLookup" data-callback="followNameLink" data-placeholder="last name, first name"></div>
				</div>
			</h2>
		</div>	
	</div>

<div id="personApp">
	<article class="owrapper">
		<div class="iwrapper">
			<h1 class="title">{{person.displayName}}
				<p v-if="person.displayBirth == '[date unknown]' && person.displayDeath == '[date unknown]'" class="dates">[dates unknown]</p>
				<p v-else class="dates">{{person.displayBirth}}-{{person.displayDeath}}</p>
			</h1>

			
			<div class="personContainer">
				<div class="nameCard">
					<p v-if="person.middle_name.length"><label>Middle name:</label> {{person.middle_name}}</p>
					<p v-if="person.maiden_name.length"><label>Birth name:</label> {{person.maiden_name}}</p>
					<p v-if="person.displayTitle.length"><label>Titles:</label> {{person.displayTitle}}</p>
					<p v-if="person.displayProfessions.length"><label>Professions:</label> {{person.displayProfessions}}</p>
					<p v-if="person.variants.length"><label>Name variants:</label> {{person.variants}}</p>
				</div>
			</div>
		</div>
	</article>

	<article class="owrapper lightFullBleed">
		<div class="iwrapper basicGutter">
			<div v-if="showDescriptions" class="descriptions">
				<h2>Descriptions</h2>
				<p v-if="person.description && person.description.length" class="description mainDescription">
					{{person.description}}
				</p>

				<div v-if="person.descriptions && person.descriptions.length" class="otherDescriptions">
					<h3>Descriptions from other Editions</h3>
					<p v-for="p in person.descriptions" class="description">
						<label>from: {{projects[p.project_id].name}}</label>
						{{p.notes}}
					</p>
				</div>
			</div>
		</div>
	</article>

	<article class="owrapper darkFullBleed">
		<div class="iwrapper basicGutter">
			<h2>Sources & Links</h2>
			<div v-if="person.links.length" class="sources">
				<div v-for="link in person.links" class="source">
					<h3>{{link.title}}</h3>
					<p v-if="link.notes && link.notes.length" v-html="link.notes"></p>
					<p v-if="link.authority_id">
						<a v-if="link.authority =='LCNAF'" :href="'https://id.loc.gov/authorities/names/' + link.authority_id + '.html'" target="_blank">View Library of Congress Name Authority File page</a>
						<a v-else-if="link.authority =='SNAC'" :href="'https://snaccooperative.org/view/' + link.authority_id" target="_blank">View Social Networks and Archival Context page</a>
					</p>
					<p v-else><a v-if="link.url && link.url.length" :href="link.url">{{link.url}}</a></p>
				</div>
			</div>
		</div>
	</article>
</div>	

<article class="owrapper">
	<div class="iwrapper basicGutter">
		<div class="documents">
			<h2>Mentioned in</h2>
			<div id="projectDocs" class="docContainer"></div>

			<div class="readAllLink">
				<a href="/publications/<? echo \MHS\Env::PROJECT_SHORTNAME;?>/read#0/20/s//p/<?=$HUSC;?>">Read all documents involving this person</a>
			</div>
		</div>

		<div class="topics">
			<h2>Mentioned in documents with these <em>Topics</em></h2>
			<div id="projectTopics"></div>
		</div>
	</div>
</article>


	<?php include("footer.php");?>

	<div id="modalMask" class="modalMask"></div>

	<script>
		let tokens = "";
		let husc = '<? echo $husc;?>';
		let topic = '<? echo $topic?>';

		window.followNameLink = function(husc){
			let url = "/publications/" + Env.projectShortname + "/explore/person/" + husc;
			location.href = url;
		}
	</script>
	<script type="module">
		import { createApp } from '/lib/vue3.2.41-dev.js?v=<?=$FRONTEND_VERSION;?>';
		import personApp from "/publications/template/js/person-app.js?v=<?=$FRONTEND_VERSION;?>";

		const app = createApp(personApp);
		app.mount("#personApp");

		buildNameLookup();

	</script>
</body>
</html>
