<?php  include("manage-head.php");

?>
<link rel="stylesheet" href="<? echo \MHS\Env::APP_INSTALL_URL;?>css/manage.css?v=<?=$FRONTEND_VERSION;?>" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
<script src="/lib/yodude.js"></script>
<script src="/lib/ui-library/dialogbox2.js"></script>
<script src="/tools/wetvac/js/pubtoolsuploader.js?v=<?=$FRONTEND_VERSION;?>"></script>
<script>
	var installDIR = "<? echo \MHS\Env::APP_INSTALL_URL;?>";
	var convertConfig = {
			"processURL": "<? echo \MHS\Env::APP_INSTALL_URL;?><? echo \MHS\Env::CONVERT_PROCESS_URL;?>",
			"dragdropBoxID": "filedrag",
			"dialogBoxID": "actionbox"
	};

	window.addEventListener("DOMContentLoaded", function(){
		var slogans = [
				"Word sucks, so we suck word.",
				"Sucking the Word out of your transcriptions.",
				"Vacuuming your transcriptions clean and dry.",
				"Sucking the MS BS out of your TEI.",
				"Schmutz goes in, schparkle comes out.",
				"It's like your transcriptions on speed.",
				"Digital Humanists do it with TEI.",
		];

		var slogan = document.getElementById("slogan");
		var r = Math.floor(Math.random() * slogans.length);

		var i=0;
		var frameRate = 45;

		function nextLetter(){
			slogan.innerHTML += slogans[r][i];
			i++;
			if(i < slogans[r].length) setTimeout(nextLetter, frameRate);
		}

		nextLetter();
	});
</script>
<script src="<? echo \MHS\Env::APP_INSTALL_URL;?>js/wetvac.js?v=<?=$FRONTEND_VERSION;?>"></script>
</head>
<body>
	<div class="masterc">
		<div class="iwrapper">
			<h1>WET VAC <strong id="slogan"></strong></h1>
		</div>
	</div>

	<div class="operations">

		<div class="wetvac">
		<div class="revdescOptions">
				<h2>Set your Revision Description options</h2>

				<p>
					<label>Transcription Milestones</label>
					<select id="transcriptionMilestone">
						<option value="x">[use marker in Word]</option>
						<option value="1">Initial transcription done</option>
						<option value="2">Verification 1 done</option>
						<option value="3">Verification 2 done</option>
					</select>
				</p>

				<p>
					<label>PersRef Milestones</label>
					<select id="persrefsMilestone">
						<option value="x">[use marker in Word]</option>
						<option value="0">Not begun</option>
						<option value="1">All persrefs added</option>
						<option value="2">Accuracy confirmed</option>
						<option value="3">HUSCS verified</option>
					</select>
				</p>

				<p>
					<label>Subjects Milestones</label>
					<select id="subjectsMilestone">
						<option value="x">[use marker in Word]</option>
						<option value="0">Not begun</option>
						<option value="1">All subjects added</option>
						<option value="2">All approved</option>
						<option value="3">All confirmed with database</option>
					</select>
				</p>

				<p>
					<label>Annotations Milestones</label>
					<select id="annotationsMilestone">
						<option value="x">[use marker in Word]</option>
						<option value="0">Not begun</option>
						<option value="1">Source note complete</option>
						<option value="2">All drafted</option>
						<option value="3">All edited</option>
						<option value="4">All approved</option>
					</select>
				</p>
			</div>

			<button id="saveMilestones">Save options</button>
			<p>Your choices will be remembered in this browser on this computer.</p>

			<form class="dropUploaderForm" enctype="multipart/form-data">
				<div class="nozzle" id="Nozzle">
					<label>Choose a .docx file from your computer</label>
					<input id="filedrag"
						type="file" multiple
						data-status-element-id="xmllist"
						data-post-url="<? echo \MHS\Env::APP_INSTALL_URL;?><? echo \MHS\Env::CONVERT_UPLOAD_URL;?>"
						data-max-file-size="8000000"/>
				</div>
			</form>

			<iframe id="autoDownload" src=""></iframe>
		</div>

		<div>
			<div class="history">
				<h2 title="The history only reflects the activities in this particular web browser on the present computer. A different history will be recorded for other users, and for you if you use a different web browser or a different computer. Your history will be lost if you clear browser data">Conversion history from this computer*</h2>
				<div id="historyContent"></div>
			</div>

		</div>

	</div>

	<footer style="margin-top: 6rem">
		<p style="font-size: 12px">* The history only reflects the activities in this particular web browser on the present computer. A different history will be recorded for other users, and for you if you use a different web browser or a different computer. Your history will be lost if you clear browser data.</p>
	</footer>

</body>
</html>
