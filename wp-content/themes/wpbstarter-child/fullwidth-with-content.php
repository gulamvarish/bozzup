<?php
/**
* Template Name: Full Width Content
 */

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<div class="container">
  <div class="row">
    <div class="col-sm-12 col-md-6 mt-4">
     <?php

       $custom_logo_id = get_theme_mod( 'custom_logo' );
       $logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
 
		if ( has_custom_logo() ) {
		    echo '<a href="/"><img src="' . esc_url( $logo[0] ) . '" alt="' . get_bloginfo( 'name' ) . '" class="pagecontentlogo"></a>';
		} 
     ?>
    </div>
  </div>
 <div class="row">
  <div class="col-12 mt-4">
    <h2><?php the_title(); ?></h2>
  </div>
  <div class="col-12 mt-4">
      <?php the_content(); ?>
  </div>
  </div>
</div>

	

	<?php endwhile; // End of the loop. ?>

<?php
get_footer();


