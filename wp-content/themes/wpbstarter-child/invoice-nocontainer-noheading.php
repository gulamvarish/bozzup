<?php
/**
* Template Name: Invoice
 */

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

	



  <div class="row m-0">
    <div class="col-sm-12 registartionleft">
    <!--   <a href="/">
    	<img class="leftlogo" src = "<?php echo plugin_dir_url('/').'register-login-custom/images/login.png'; ?>" alt="Dashboard"/>
    </a> -->
    	
    	
    	<?php 

      
      the_content(); 


      ?>

    </div>   
   
    
  </div>

	<?php endwhile; // End of the loop. ?>

<?php
get_footer();
