<?php ?><!DOCTYPE html>
<html lang="en">
<head>
<?php
	require_once("head.php");
?>
	<link rel="stylesheet" href="/publications/template/css/theme1/tei.css?v=<?=$FRONTEND_VERSION;?>" />

</head>
<body class="innerPage <?=$bodyClasses;?> document">
	<div class="betamark">BETA</div>

	<?php include(\MHS\Env::INCLUDES_PATH . "header.html");?>

	<article class="owrapper">

		<div class="iwrapper">
			<div class="navTools">
				<div class="innerNavTools">
					<div class="previous" v-cloak>
						<div class="docChoices">
						</div>
					</div>

					<div id="docTitle" class="docTitle">
						<p>Sorry, that document is not available for viewing.</p>
					</div>

					<div class="next" v-cloak>
						<div class="docChoices">
							<div class="docChoices">
							</div>
						</div>
					</div>
				</div>
			</div>	



			<div class="docSection">
				<div id="document" class="teiFragment">
				</div>
			</div>



		</div>
	</article>

	<?php include("footer.php");?>
</body>
</html>
