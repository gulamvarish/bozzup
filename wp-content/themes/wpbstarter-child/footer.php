<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package wpbstarter
 */

 if(!is_page_template( 'blank-page.php' ) && !is_page_template( 'blank-page-with-container.php' )&& !is_page_template( 'fullwidth-nocontainer-noheading.php' )): ?>

	</div><!-- #content -->

    <?php get_template_part( 'footer-widget' ); ?>
	<footer id="colophon" class="site-footer">
		<div class="site-info">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-4 col-md-6 col-sm-6 d-flex justify-content-center justify-content-md-start">
						<a href="<?php echo home_url(); ?>">
							<?php
							 echo "&copy; Copyright 2021, BozzUp";
							?>
						</a>
					</div>
					<div class="col-lg-8 col-md-6 col-sm-6 d-flex justify-content-center justify-content-md-end">
						<small class="float-right"> FRANCESCO DE LEO, TRIQ SANTA MARTA, RABAT, VCT2551, GOZO, MALTA</small>
					</div>
				</div>
			</div>


		</div><!-- .site-info -->
	</footer><!-- #colophon -->
<?php endif;

global $current_user;

$name      = $current_user->user_login;
$email     = $current_user->user_email;
$company   = get_user_meta($current_user->ID, 'company', true);

 ?>
</div><!-- #page -->

<!-- for mail message -->
<button type="button" class="btn btn-primary d-none mailModal" data-toggle="modal" data-target="#mailModal">   
  </button>
<div class="modal fade helpemail" id="mailModal">
    <div class="modal-dialog">
      <div class="modal-content">  
      <div class="modal-header">
        <h4 class="modal-title">Help Email</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>   
              
        <!-- Modal body -->
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <div class="modal-body">
        
            <form id="helpmailform">
						  <div class="form-group">
						    <label for="formName">Name</label>
						    <input type="text" name="name" class="form-control" id="formName" value="<?php echo $name; ?>" readonly>

						    <input type="hidden" name="role" class="form-control" id="role" value="<?php echo $_SESSION['user_type']; ?>" readonly>
						  </div>
						  <div class="form-group">
						    <label for="formemail">Email</label>
						   <input type="email" name="email" class="form-control" id="formemail" value="<?php echo $email; ?>" readonly>
						  </div>
						   <div class="form-group">
						    <label for="formcompany">Company Name</label>
						   <input type="email" name="company" class="form-control" id="formcompany" value="<?php echo $company; ?>" readonly>
						  </div>
						   <div class="form-group">
						    <label for="formmessage">Message</label>						   
						   <textarea  name="message" class="form-control message" id="formmessage" value="" required="" rows="3"></textarea>
						  </div>
						  <button type="button" id="helpemailsend" class="btn btn-primary emailhelpbtn">Submit</button>
						</form>
        </div>
        
        <!-- Modal footer -->
        
        
      </div>
    </div>
  </div>


<?php wp_footer(); ?>

</body>
</html>
