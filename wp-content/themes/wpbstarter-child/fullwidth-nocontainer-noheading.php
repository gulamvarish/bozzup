<?php
/**
* Template Name: Full Width No Container No Heading
 */

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

	



  <div class="row m-0">
    <div class="col-sm-12 registartionleft">
      <a href="/">
    	<img class="leftlogo" src = "<?php echo plugin_dir_url('/').'register-login-custom/images/login.png'; ?>" alt="Dashboard"/>
    </a>
    	<h4>Create an account</h4>
    	
    	<?php 

      
      the_content(); 


      ?>

    </div>
    <!-- <div class="col-sm-6 loginright">
 	
<img class="loginimage1" src = "<?php echo plugin_dir_url('/').'register-login-custom/images/img2.png'; ?>" alt="Dashboard"/>
<img class="loginimage2" src = "<?php echo plugin_dir_url('/').'register-login-custom/images/img1.png'; ?>" alt="Dashboard"/>
<div class="col-sm-12 logintext">

	<h4>Easy Manage and Organize</h4>
	<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita</p>

</div>
<img class="loginimage3" src = "<?php echo plugin_dir_url('/').'register-login-custom/images/img3.png'; ?>" alt="Dashboard"/>
<img class="loginimage4" src = "<?php echo plugin_dir_url('/').'register-login-custom/images/img4.png'; ?>" alt="Dashboard"/>
<img class="loginimage5" src = "<?php echo plugin_dir_url('/').'register-login-custom/images/img5.png'; ?>" alt="Dashboard"/>
<img class="loginimage6" src = "<?php echo plugin_dir_url('/').'register-login-custom/images/img6.png'; ?>" alt="Dashboard"/>

 </div> -->
   
    
  </div>

	<?php endwhile; // End of the loop. ?>

<?php
get_footer();
