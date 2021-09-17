<?php
/**
* Template Name: Full Width With Container
 */

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

	

	<div id="primary" class="content-area wpbstarter-content-area-padding">
		<main id="main" class="site-main">
			
		<div class="row" style="background-color:#FFFFFF !important;color:#000000 !important;">
					
						<?php

							the_content();

						
						?>
					
			< 		
    </div>
		</main><!-- #main -->
	</div><!-- #primary -->

	<?php endwhile; // End of the loop. ?>

<?php
get_footer();


