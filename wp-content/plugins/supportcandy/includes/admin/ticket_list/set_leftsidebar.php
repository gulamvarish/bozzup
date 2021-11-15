<div id="wpsc_sm_filters1" class="col-sm-12 col-md-2 wpsc_sidebar <?php echo $_SESSION['user_type']; ?>">
		<div class="row">	
		<div class="sidebarlogo">
			
			 <a href="/"><img class="logobottomimage" src="<?php echo plugin_dir_url('/').'supportcandy/asset/images/login.png'; ?>"></a>
		</div>
		<hr>			 
			<?php 

			//print_r($current_user->roles);

			/*echo $_SESSION['user_type'];
			echo $_SESSION['expire'];*/


				if(($current_user->roles[0]=='administrator' || $_SESSION['user_type']=='supplier') && $_SESSION['expire'] !='account-expire') {
		?>				 
			  <a href="/" class="sidebar-link"><img onclick="sidebar_menu();" src = "<?php echo plugin_dir_url('/').'supportcandy/asset/images/icon/dashboard.png'; ?>" alt="Dashboard"/><p class="text">Dashboard</p></a>
			  <a href="/customers" class="sidebar-link"><img onclick="sidebar_menu();" src = "<?php echo plugin_dir_url('/').'supportcandy/asset/images/icon/customer.png'; ?>" alt="Customers"/><p class="text">Customers</p></a>
			  <a href="/membership-account" class="sidebar-link"><img onclick="sidebar_menu();" src = "<?php echo plugin_dir_url('/').'supportcandy/asset/images/icon/setting.png'; ?>" alt="Setting"/><p class="text">Setting</p></a>
			   
			 
			  <?php }elseif($_SESSION['user_type']=='subscriber'){ ?>

			  	 <a href="/edit-user?id=<?php echo $current_user->ID; ?>" class="sidebar-link"><img onclick="sidebar_menu();" src = "<?php echo plugin_dir_url('/').'supportcandy/asset/images/icon/customer.png'; ?>" alt="Customers"/><p class="text">Edit Profile</p></a>

			  <?php }  ?>
			  <a href="javascript:void(0)" id="helpemail" class="sidebar-link mailModal" data-toggle="modal" data-target="#mailModal"><img onclick="sidebar_menu();" src = "<?php echo plugin_dir_url('/').'supportcandy/asset/images/icon/help.png'; ?>" alt="help"/><p class="text">Help</p></a> 


		<?php 
  
   if(($_SESSION['user_type']=='supplier') && $_SESSION['expire'] =='account-expire'){
			 global $wpdb;
			$wpdb_prefix = $wpdb->prefix;
			$wpdb_tablename = $wpdb_prefix.'pmpro_memberships_users';

			$sql = 'SELECT * FROM '.$wpdb_tablename.' WHERE `user_id` = '.$current_user->ID.' ORDER BY `id` DESC';
			$result = $wpdb->get_results($sql);

			if($result[0]->membership_id==4){

                 $level = 1;

			}else{

                $level = $result[0]->membership_id;
			}
			

			 ?>

			 <a href="<?php echo home_url('/')?>membership-account/membership-checkout/?level=<?php echo $level; ?>&&renew=1"  class="sidebar-link"  ><img src = "<?php echo plugin_dir_url('/').'supportcandy/asset/images/icon/help.png'; ?>" alt="help"/><p class="text">Renew</p></a>

    <?php  }

		$wpsc_support_page_id = get_option('wpsc_support_page_id');
		$support_page_url = get_permalink($wpsc_support_page_id);
		$wpsc_allow_sign_out = get_option('wpsc_sign_out');
		if($wpsc_allow_sign_out){?>
			<button class="btn btn-sm pull-right" type="button" id="wpsc_sign_out" onclick="window.location.href='<?php echo wp_logout_url($support_page_url) ?>'" style="background-color:#FF5733 !important;color:#FFFFFF !important;"><i class="fas fa-sign-out-alt"></i> <?php _e('Log Out','supportcandy')?></button>
		<?php  }?>

		 
					
		</div>
		
	</div>