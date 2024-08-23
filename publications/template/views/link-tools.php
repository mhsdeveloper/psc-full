<?php 
	session_start();
	$project = str_replace("/", "", $_SESSION['PSC_SITE']);

	$FRONTEND_VERSION = "2b";
	
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
	<title>COOP LINK TOOL</title>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital@0;1&family=Roboto:ital,wght@0,100;0,400;0,700;1,100;1,400;1,700&display=swap" rel="stylesheet">
	<script src="/publications/template/js/coophelpers.js?v=<?=$FRONTEND_VERSION;?>"></script>
	<script src="/publications/template/js/yodude.js?v=<?=$FRONTEND_VERSION;?>"></script>
	<script src="/publications/template/js/name-lookup.js?v=<?=$FRONTEND_VERSION;?>"></script>
	<script src="/projects/coop/customize/custom.js?v=<?=$FRONTEND_VERSION;?>"></script>
	<script>
		const Env = {
			apiURL: "/mhs-api/u1/",
			apiExtURL: "/mhs-api/ext/",
			projectShortname: "<?=$project;?>",
			projectID: <?=$_SESSION['PSC_PROJECT_ID']?>
		}
	</script>
<?php
//	require_once("head.php");
?>
	<style>
		body {
			font-family: "Roboto", sans-serif;
		}

		button {
			border: 0;
			background: #0074d7;
			color: white;
			padding: 0.5rem 1rem;
			border-radius: 4px;
			font-size: 16px;
			display: block;
			width: 90%;
			margin: 0 auto .25rem auto;
		}

		input {
			padding: 4px 8px;
		    font-size: 18px;
		}

		input#output {
			border: 2px solid blue;
			background: #c9ddff;
		}

		.bigText {
			display: block;
			margin: auto;
			width: 92%;
			font-size: 20px;
		}

		.tool {
			display: none
		}
		.tool.selected {
			display: block;
		}

		#notice {
			bottom: -100px;
			position: absolute;
			z-index: 100;
			background: blue;
			color: white;
			padding: 1rem;
			transition: bottom .5s ease;
		}
		#notice.show {
			bottom: 0;
		}

		#arrow {
			display: block;
			width: 100px;
			margin: auto;
			margin-top: 2rem;
			transform: rotate(90deg);
		}

		#output {
			display: inline-block;
		    width: 65%;
		}

		svg {
			transform: scale(.5);
			display: inline-block;
			vertical-align: bottom;
			top: 13px;
			position: relative;
		}

		#copy {
			background: #007eff;
			/* stroke: white; */
			fill: white;
			padding: 1rem 1rem;
			border-radius: 15px;
			display:block;
			margin-top: 1rem;
		}

		.hidden {
			display: none;
		}

	</style>
</head>
<body class="innerPage ">
	<h1>Link Tools</h1>

	<button id="personLink" class="choice" data-sec="personLinkForm" title="Generate link to automatic, metadata based summary page">Link to a Person page</button>
	<!-- <button id="projectNames" class="choice" data-sec="projectNamesForm">Code to pull in project names</button>
	<button id="nameCard" class="choice" data-sec="nameCardForm">Code to insert name card</button> -->

	<div id="personLinkForm" class="tool">
		<p>This will create the link for an automatic person summary page, the page that uses the HUSC to automatically generate summary information and metadata lists.</p>
		<h3>Look up a person from the names database:</h3>
		<div class="lookup findPerson">
			<div class="bigText nameLookup" data-callback="chooseNameLink" data-placeholder="last name, first name"></div>
		</div>

		<div id="linkStep2" class="hidden">
			<!-- <input class="nameLookup bigText" data-callback="chooseNameLink" placeholder="last name, first name"/><br/> -->
			<h3>The clickable text of the link is shown below. Change the text as you see fit:</h3>
			<input class="bigText" id="nameLinkText" placeholder="name can be formatted here..."/>
		</div>
	</div>



	<div id="projectNamesForm" class="tool">
		<p>This will create the code that you paste into Word Press pages so that those pages pull in metadata displays.</p>
	</div>

	<div id="nameCardForm" class="tool">
		<p>This will create the code to paste into Word Press pages where you wish to insert a name card for a person.</p>
		<h3>Look up a person from the names database</h3>
		<div class="bigText nameLookup" data-callback="chooseNameCard" data-placeholder="last name, first name"></div>
		<!-- <input class="bigText nameLookup" data-callback="chooseNameCard" placeholder="last name, first name"/> -->
	</div>



	<div class="output hidden">
		<h3>Click the blue box below to copy the link, after which you can paste anywhere in a WordPress page.</h3>
		<svg id="arrow" xmlns="http://www.w3.org/2000/svg" height="48" width="48"><path d="m12.1 38 10.5-14-10.5-14h3.7l10.5 14-10.5 14Zm12.6 0 10.5-14-10.5-14h3.7l10.5 14-10.5 14Z"/></svg>
		<input readonly="readonly" id="output"/>
<!-- <svg id="copy" xmlns="http://www.w3.org/2000/svg" height="48" width="48"><path d="M9 43.95q-1.2 0-2.1-.9-.9-.9-.9-2.1V10.8h3v30.15h23.7v3Zm6-6q-1.2 0-2.1-.9-.9-.9-.9-2.1v-28q0-1.2.9-2.1.9-.9 2.1-.9h22q1.2 0 2.1.9.9.9.9 2.1v28q0 1.2-.9 2.1-.9.9-2.1.9Zm0-3h22v-28H15v28Zm0 0v-28 28Z"/></svg> -->
		<button id="copy">COPY</button>
	</div>


	<div id="notice"></div>

	<script>
		let searchAPIURL = "/publications/" + Env.projectShortname + "/searchQuery";

		let linkText = "";
		let husc = "";

		let notice = document.getElementById("notice");
		let tools = document.getElementsByClassName("tool");
		let out = document.getElementById("output");
		let linkTextEl = document.getElementById("nameLinkText");

		let outDiv = document.getElementsByClassName("output")[0];
		let linkStep2 = document.getElementById("linkStep2");
		
		linkTextEl.addEventListener("keyup", ()=>{
			linkText = linkTextEl.value;
			updateNameLink(husc);
		});


		function chooseNameLink(h, nameObj){
			husc = h;
			linkText = CoopHelpers.nameMetadataFormat(nameObj);
			linkTextEl.value = linkText;
			updateNameLink(h);
			outDiv.classList.remove("hidden");
			linkStep2.classList.remove("hidden");
		}

		function updateNameLink(h){
			let text = `<a href="/publications/` + Env.projectShortname + `/explore/person/${h}">${linkText}</a>`;
			out.value = text;
		}

		function updateLinkText(text){
			linkText = text;
		}

		function chooseNameCard(husc){
			let text = "<code>name-card:" + husc + " </code>";
			out.value = text;
		}
		
		
		function showNotice(text){
			notice.innerHTML = text;
			notice.classList.add("show");
			setTimeout(()=>{notice.classList.remove("show")}, 4000);
		}


		function closeTools(){
			out.value = "";
			outDiv.classList.add("hidden");
			linkStep2.classList.add("hidden");

			for(let tool of tools){
				tool.classList.remove("selected");
			}
		}

		let set = document.getElementsByClassName("choice");
		for(let b of set){
			b.addEventListener("click", (e)=>{
				closeTools();
				let id = e.target.getAttribute("data-sec");
				let sec = document.getElementById(id);
				sec.classList.add("selected");

				if(id == "projectNamesForm"){
					out.value = "<code>project-names:</code>";
				}
			});
		}


		function copy(){
			out.select();
			out.setSelectionRange(0, 99999);
			document.execCommand('copy');
			showNotice("Copied to clipboard!");
		}

		out.addEventListener("click", copy);

		document.getElementById("copy").addEventListener("click", copy);

		buildNameLookup();
	</script>
</body>
</html>