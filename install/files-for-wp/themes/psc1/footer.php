<?php
/**
 * The template for displaying the footer
 *
 */

?>
	<footer>
		<?php 

			preg_match("/https?\:\/\/[^\/]+\/([a-z0-9]+)\//", esc_url( home_url( '/' ) ), $matches );
			if(!isset($matches[1]) || empty($matches[1])) $matches[1] = "coop";
			include($_SERVER['DOCUMENT_ROOT']. "/projects/" . $matches[1] . "/support-files/footer.html");
		?>
		<p class="copyright">&copy; <? echo date("Y")?> Primary Source Cooperative at the Massachusetts Historical Society</p>
	</footer>
	<script>
	(function(){
		if(false === location.href.includes("primarysourcecoop.org")){
			let div = document.createElement("div");
			div.style="background: red; color: white; font-weight: bold; font-size: 24px; padding: 1rem";
			div.innerHTML = "THIS IS NOT THE MAIN, LIVE SERVER; <span style='font-size: 85%'> this is a test server.</span>";	
			document.body.insertBefore(div, document.body.firstChild);
		}
	})();
</script>