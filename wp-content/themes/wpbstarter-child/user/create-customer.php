<?php
/**
* Template Name: Create Customer
 */

get_header(); 


page_access()
?>

<?php while ( have_posts() ) : the_post(); ?>

	


	<div id="primary" class="content-area wpbstarter-content-area-padding">
		<main id="main" class="site-main">
			<div class="container">
			<div class="row">
                    <div class="col-12">
                    	<div class="col-12 text-right"><a class="btn btn-primary" href="">Add Customer</a></div>
                    	
							<table id="user" class="table table-striped table-bordered dt-responsive" style="width:100%" url="<?php echo home_url();?>/get_User";>
						        <thead>
								    <tr role="row">
								      <th>Username</th>
								      <th>Name</th>
								      <th>Email</th>
								      <th>Company Name</th>
								      <th>Register Date</th>
								      <th>Action</th>
								    </tr>
								 </thead>
								  <tbody>
								</tbody>
					    </table>
					</div>
				</div>	
			</div>

		</main><!-- #main -->
	</div><!-- #primary -->

	<?php endwhile; // End of the loop. ?>

<?php
get_footer();
