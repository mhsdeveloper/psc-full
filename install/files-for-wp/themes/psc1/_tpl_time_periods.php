<?php
 /* Template Name: Time Periods Template */ 
 /**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage PSC_1
 * @since PSC1 1.0
 */
?><? include("head.php");?>


<body class="timePeriods <?=$body_classes; ?>">


<?php //wp_body_open(); ?>


	<div id="page" class="site">


		<? include("header.php"); ?>


		<div id="content" class="site-content">
			<?

			/* Start the Loop */
			while ( have_posts() ) {
				the_post();
			?>
				<div class="iwrapper"><?	the_title( '<h1 class="title">', '</h1>' ); ?></div>
				
				<article id="post-<? the_ID(); ?>" <? post_class(); ?>>
				
					<div class="entry-content">
						<?
							the_content();
					
							wp_link_pages(
								array(
									'before'   => '<nav class="page-links" aria-label="' . esc_attr__( 'Page', 'primarysourceone' ) . '">',
									'after'    => '</nav>',
									/* translators: %: Page number. */
									'pagelink' => esc_html__( 'Page %', 'primarysourceone' ),
								)
							);
						?>
					</div><!-- .entry-content -->
				</article>


				<div class="timePeriodXML wp-block-group__inner-container" id="timePeriods">
					<div id="timePeriodHeadings"></div>

					<?php print file_get_contents(\MHS\Env::SUPPORT_FILES_PATH . "timeperiods.xml");?>
				</div>


			<?

				// If comments are open or there is at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}

				
			} // End of the loop.
			?>

			<? if ( is_active_sidebar( 'sidebar-1' ) ) { ?>
				<aside class="widget-area">
					<? dynamic_sidebar( 'sidebar-1' ); ?>
				</aside><!-- .widget-area -->
			<? } ?>


		</div><!-- #content -->

	<? 
		get_template_part( 'template-parts/footer/footer-widgets' ); 
		get_footer();
	?>


	</div><!-- #page -->
	<script>

		function jumpToPeriod(e){
			let el = e.target;
			while(el && el.nodeName != "BUTTON") el = el.parentNode;
			if(!el || !el.period) return;
			let rect = el.period.getBoundingClientRect();
			window.scrollTo(0, rect.top);
		}

		let tp = document.getElementById("timePeriods");
		let tph = document.getElementById("timePeriodHeadings");
		let set = tp.getElementsByTagName("heading");
		for(let i=0;i<set.length;i++){
			let a = document.createElement("button");
			a.innerHTML = set[i].innerHTML;
			let sdate = set[i].nextElementSibling;
			let edate = sdate.nextElementSibling;
			a.innerHTML += "<span class='dates'>" + sdate.innerHTML + "-" + edate.innerHTML + "</span>";

			a.period = set[i];
			a.addEventListener("click", jumpToPeriod);
			tph.appendChild(a);
		}

		set = tp.getElementsByTagName("startdate");
		for(let el of set){
			let startdate = el.getAttribute('date');
			let enddate = el.nextElementSibling.getAttribute('date');
			startdate = "" + startdate.replace("-", "");
			enddate = "" + enddate.replace("-", "");
			while(startdate.length < 8) startdate += 0;
			while(enddate.length < 8) enddate += 9;

			console.log(startdate + " - " + enddate);

			let searchStr = "search#q%3D%2Bdate_when%3A%5B" + startdate + "%20TO%20" + enddate;

			let a = document.createElement("a");
			a.href = "/publications/" + Env.projectShortname + "/" + searchStr;
			a.innerHTML = "Search documents from this period";
			a.className = "searchLink";
			el.parentNode.appendChild(a);
		}
	</script>
</body>
</html>
