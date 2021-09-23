<?php
/**
* Template Name: Customers
 */

get_header(); 


page_access()
?>

<?php while ( have_posts() ) : the_post(); ?>

	


	<div id="primary" class="content-area wpbstarter-content-area-padding">
		<main id="main" class="site-main">
			

		</main><!-- #main -->
	</div><!-- #primary -->

	<?php endwhile; // End of the loop. ?>

<?php
get_footer();
