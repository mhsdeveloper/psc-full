<?php
	require_once("head.php");
?>
<script>
	var customViewConfig = {
		tabifyHeader: false
	}
</script>
	<script src="/lib/proofReader.js"></script>
	<!-- <link rel="stylesheet" href="/publications/lib/sass/theme1/proof/proof.css"/> -->
	<link rel="stylesheet" href="/publications/pub/css/authorview_rev2.css"/>
	<style>

		body {
			padding: 2.5rem;
		}

		.documentID {display: none}

		article {
			margin: auto;
			max-width: 1100px;
		}
	</style>
</head>
<body class="innerPage proof <?=$bodyClasses;?>">
	<h3 class="proofNotice">Editor Proofreading View</h3>
	<div class="wrapper">
		<article>
			<div id="document" class="teiFragment"><?=$document;?></div>
		</article>
	</div>

</body>
</html>
