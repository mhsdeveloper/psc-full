<?php /* Template Name: Component */
/**
 */

/* Start the Loop */
while ( have_posts() ) :
	the_post();
	the_content();

endwhile; // End of the loop.
