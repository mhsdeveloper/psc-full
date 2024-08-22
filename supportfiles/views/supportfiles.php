<?php

	$FRONTEND_VERSION = 17;


?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><? echo \MHS\Env::PROJECT_HTML_TITLE;?></title>
<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons" rel="stylesheet" type="text/css">
<link href="/lib/quill.snow.css" rel="stylesheet">
<script src="/lib/quill.js"></script>

<link href="/lib/quasar.2.12.4.prod.css" rel="stylesheet" type="text/css">
<script src="/lib/yodude.js?v=<?=$FRONTEND_VERSION;?>"></script>
<link rel="stylesheet" href="<? echo PATH_ABOVE_WEBROOT;?>css/style.css?v=<?=$FRONTEND_VERSION;?>"/>
<script>
	let Env = {
		baseURL: "<? echo PATH_ABOVE_WEBROOT;?>",
		projectID: <? echo \MHS\Env::PROJECT_ID;?>,
		username:  "<?=$_SESSION['PSC_USER'];?>",
		role: "<?=$_SESSION['PSC_ROLE'];?>",
		projectShortname: "<? echo \MHS\Env::PROJECT_SHORTNAME;?>",
	}
</script>
</head>
<body class="documentManager">
	<div class="masterc">
		<div id="app" class="app">
			<h1>Support File Manager</h1>

			<q-dialog v-model="logShowing">
				<q-card style="min-width: 350px">
					<div class="statusLog" :class="statusDialogMode">
						<q-card-section class="q-pt-lg">
							<h4>{{statusTitle}}</h4>

							<q-linear-progress rounded size="20px" :value="reindexProgress" color="warning" class="q-mt-sm"></q-linear-progress>

							<div v-for="(l, index) in log" class="fileReport"
								v-bind:class="'l' + l.type" v-html="l.text">
							</div>
						</q-card-section>
					</div>
				</q-card>
			</q-dialog>


			<template v-if="Env.role == 'administrator'">
				<div class="toolsUploader">
					<q-file filled multiple v-model="uploadFiles" label="Upload new files">
						<template v-slot:prepend>
							<q-icon name="cloud_upload" @click.stop.prevent></q-icon>
						</template>
						<q-tooltip anchor="bottom middle" content-style="max-width: 300px; font-size: 16px" self="top middle" :offset="[10, 10]">Upload a new file</q-tooltip>
					</q-file>
				</div>
			</template>


			<section class="filelist">
				<div v-for="file in files" class="file"
				>
					<button @click="deleteFileCheck(file.name)" class="darkStyle">delete</button>
					<button @click="download(file.name)">download</button>
					<button v-if="file.name.includes('.html')" @click="edit(file.name)" class="actionStyle">edit</button>

					{{file.name}}
				</div>
			</section>


			<section :class="editorShowing ? 'editorCont' : 'hidden'">

				<p style="padding: 2rem">To format the footer: keep separate items (such as patron logos) on separate lines. use "H2" for the name of your edition; this should be the first element in the footer. </p>

				<div class="toolbar">
					<button @click="saveHTML" class="actionStyle">Save</button>
					<button @click="cancelEdit" class="noticeStyle">Cancel</button>
				</div>

				<div id="editor">
				</div>

			</section>

			<q-dialog v-model="confirmShowing" persistent>
				<q-card>
					<q-card-section class="row items-center">
						{{confirmText}}
					</q-card-section>

					<q-card-actions align="right">
						<q-btn @click="cancelDelete" flat label="Cancel" color="primary" v-close-popup></q-btn>
						<q-btn @click="deleteFile" flat label="DELETE" color="primary" v-close-popup></q-btn>
					</q-card-actions>
				</q-card>
			</q-dialog>



		</div><!-- END APP -->






	</div>
	<? /* third-party dependencies loaded in head.php */?>
	<script src="/lib/vue.3.3.4.global.js"></script>
	<script src="/lib/quasar.2.12.4.umd.prod.js"></script>
	<script src="<? echo PATH_ABOVE_WEBROOT;?>js/app.js?v=<?=$FRONTEND_VERSION;?>"></script>
	<script>
		const { createApp, ref } = Vue;
		App.data = function(){ return AppData;}

		let VueApp = createApp(App);
		VueApp.use(Quasar);
		VueApp.mount("#app");
	</script>
</body>
</html>