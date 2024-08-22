<?php include("head.php"); ?>

</head>
<body class="documentManager">
	<div class="masterc">
	<? if(is_readable(__DIR__ . "/../customize-frontend/docmanager.php")) {
			include __DIR__ . "/../customize-frontend/docmanager.php";
		} else { 
			include("docmanager.php");
		}
	?>
	</div>
	<script src="<? echo PATH_ABOVE_WEBROOT;?>js/app.js?v=<?=$FRONTEND_VERSION;?>"></script>
	<script src="<? echo PATH_ABOVE_WEBROOT;?>js/appMethods.js?v=<?=$FRONTEND_VERSION;?>"></script>
	<? 
		foreach (glob(__DIR__ . "/../customize-frontend/*.js") as $jsfile) { 
			$jsfile = str_replace($_SERVER['DOCUMENT_ROOT'], "", $jsfile);
			?>
			<script src="<?=$jsfile;?>"></script>
	<? } ?>
	<script src="<? echo PATH_ABOVE_WEBROOT;?>js/appSearch.js?v=<?=$FRONTEND_VERSION;?>"></script>
	<script src="/lib/vue.3.3.4.global.js"></script>
	<?php include("quasarSetup.php");?>
	<script>
		const { createApp, ref } = Vue;

		App.data = function(){ return AppData;}

		let DocApp = createApp(App);
		DocApp.use(Quasar);
		DocApp.mount("#docapp");
	</script>
</body>
</html>