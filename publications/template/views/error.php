<?php ?><!DOCTYPE html>
<html lang="en">
<head>
<?php
	require_once("head.php");
?>
</head>
<body>
	
	<?php include("header.php");?>

	<article>
		<h1>Sorry, something went wrong.</h1>


		<section>
			<h2>Here's what we can tell you:</h2>
			
			<p>
				<?=$errors?>
			</p>
			
		</section>
	</article>

	<?php include("footer.php");?>

</body>
</html>

