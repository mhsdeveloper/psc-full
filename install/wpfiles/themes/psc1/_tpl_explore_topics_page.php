<?php
 /* Template Name: Explore Topics Template */ 
 /**
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage PSC_1
 * @since PSC1 1.0
 */
?><?	include("head.php");?>

<body class="explore topicsPage searchTypePage <?=$body_classes; ?>">


<?php //wp_body_open(); ?>


	<div id="page" class="site">


		<? include("header.php"); ?>


		<div id="content" class="site-content">
			<?

			/* Start the Loop */
			while ( have_posts() ) {
				the_post();
			?>
				<div class="iwrapper">
					<h1 class="title">Explore Topics
						<!-- <div class="lookup findPerson">
							<label>Find a person:</label>
							<div class="nameLookup" data-callback="followNameLink" data-placeholder="last name, first name"></div>
						</div> -->
					</h1>
				</div>				

				
				<article <? post_class(); ?> id="topicsApp">
	
					<div class="basicGutter inner-container">
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

					<topics-lookup ref="topicbox" :project="project" @add-topic="chooseTopic" @close="closeTopic()"></topics-lookup>
					<div :class="showNames || showTopics ? 'lookupMask' : 'hidden'" @click="closeTopic(); showNames = false"></div>

					<div class="basicGutter inner-container mostFrequent">
						<h2>Most Frequently Occuring Topics</h2>
						<div class="popularTopics">
							<span  v-for="topic in popularTopics"
								class="topic" tabindex="0"
								@keyup.enter="getCompleteTopic(topic.name)"
								@click="getCompleteTopic(topic)"
							>
								{{topic}}, <span class="count">{{allSOLRTopics[topic]}} document{{allSOLRTopics[topic] > 1 ? "s" : ""}}</span>
							</span>
						</div>
					</div>

					<div class="basicGutter inner-container mostEditions">
						<h2>Most Broadly Used Topics</h2>
						<div class="popularTopics">
							<template v-for="topic in crossEditionTopics">
								<h3 v-if="topic.label">{{topic.editions.length}} Editions Using</h3>
								<span class="topic">
									<span tabindex="0" 
									@keyup.enter="getCompleteTopic(topic.name)"
									@click="getCompleteTopic(topic.name)"
									>{{topic.name}},</span> 
									<span v-for="edition in topic.editions" class="edition"
									 :title="projects[projects.nameToID[edition.toLowerCase()]].name"> {{edition}} </span>
								</span>
						</template>
						</div>
					</div>


					<div class="basicGutter inner-container">
						<h2>All Topics, <span class="lesser">alphabetically</span></h2>
						<div class="topicsList" >
							<div v-for="letter in letters" class="letterGroup">
								<h3>{{letter}}</h3>
								<a  v-for="topic in allTopics[letter]"
									class="topic" tabindex="0"
									@keyup.enter="getCompleteTopic(topic.name)"
									@click="getCompleteTopic(topic)"
									tabindex="0"
								>
								{{topic}}, <span class="count">{{allSOLRTopics[topic]}} document{{allSOLRTopics[topic] > 1 ? "s" : ""}}</span>
								</a>
							</div>
						</div>
					</div>

				</article>
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
	<div id="modalMask" class="modalMask"></div>

	<script type="module">
		import { createApp } from '/lib/vue3.2.41-dev.js?v=<?=$FRONTEND_VERSION;?>';
		import Topics from "/publications/template/js/topics-vue.js?v=<?=$FRONTEND_VERSION;?>";
		const App = createApp(Topics);
		App.mount("#topicsApp");
	</script>
</body>
</html>
