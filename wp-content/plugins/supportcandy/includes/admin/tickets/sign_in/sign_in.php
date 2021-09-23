<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit; // Exit if accessed directly

}

$enable_login_settings = get_option('wpsc_default_login_setting');

$custom_login_url      = get_option('wpsc_custom_login_url');

$wpsc_appearance_login_form=get_option('wpsc_appearance_login_form');



$general_appearance = get_option('wpsc_appearance_general_settings');



$signin_button_css = 'background-color:'.$wpsc_appearance_login_form['wpsc_signin_button_bg_color'].' !important;color:'.$wpsc_appearance_login_form['wpsc_signin_button_text_color'].' !important;border-color:'.$wpsc_appearance_login_form['wpsc_signin_button_border_color'].' !important;';

$wpsc_login_captcha 			= get_option('wpsc_login_captcha');

$wpsc_recaptcha_type       		= get_option('wpsc_recaptcha_type');

?>

<div class="container loginmaincontainer">

  <div class="row login-row"> 	
 <?php 	

if( $_REQUEST['checkemail'] == 'confirm'){

	echo "<div class='confirmemail col-md-12 text-center bg-success mb-3 p-1' style='color:#fff'>We sent an email to you with your login information and a link to reset
your password. Back to <a href='/' style='color:#fff'>login</a></div>";
} ?>

<div class="login-right col-md-8">

	

  <div class="logo-img"><a href="/"><img  src="<?php echo plugin_dir_url('/').'supportcandy/asset/images/login.png'; ?>"></a></div>

  <h3 class="logo-img-title">Easily manage, organize and keep under control the review and approval of your customers’ mockups with just one glance and few clicks!</h3>



  
  <a href="<?php echo home_url(); ?>/registration-supplier/?level=4" class="btn freetrial" role="button">Start your free trial</a>
  <p>Try it free for 14 days. No credit card required.</p>

</div>

<div class=" col-md-4">

	<div class="login-form-div">



<?php 



do_action('wpsc_before_signin_module');

if($enable_login_settings=='1') {?>

	

		<!-- <h2 class="form-signin-heading text-center mt-4 mb-4 pt-4 pb-4" style="border-bottom:1px solid #ccc;"><?php //echo __('Please sign in','supportcandy')?></h2> -->

		

		<form id="frm_wpsc_sign_in" action="javascript:wpsc_sign_in();" method="post" style="margin-bottom:5px;">

			<p id="wpsc_message_login" class="bg-success" style="display:none;"></p>

			<div class=" col-md-12 p-0">

			<div class=" col-md-6 p-0 text-center">

			 <input type="radio" class="btn-check d-none" name="user_type" id="option1" value="subscriber" checked="" onclick="wpsc_user_type(this);">

       <label class="btn btn-secondary customersupplier customerlogin" for="option1">Customer</label>

     </div>

     <div class=" col-md-6 p-0 text-center">

       <input type="radio" class="btn-check d-none" name="user_type" id="option2" value="supplier" onclick="wpsc_user_type(this);">

       <label class="btn btn-secondary customersupplier supplierlogin" for="option2">Supplier</label>

       </div>
       <span id="usertypemesg">I am a customer, I have mockups to approve from my suppliers.</span>

     </div>

     

			<label class="sr-only"><?php echo __('Username or email','supportcandy')?></label>

			<input id="inputEmail" name="username" class="form-control mb-3" placeholder="<?php echo __('Username or email','supportcandy')?>" required="" autofocus="" autocomplete="off" type="text" value="<?php echo isset($_POST['username']) ? esc_attr($_POST['username']) : '';?>">

			<label for="inputPassword" class="sr-only"><?php echo __('Password','supportcandy')?></label>

			<input id="inputPassword" name="password" class="form-control mb-3" placeholder="<?php echo __('Password','supportcandy')?>" required="" autocomplete="off" type="password">

			<div class="checkbox">

					<label>

							<input name="remember" value="remember-me" type="checkbox"> <p  style="color:<?php echo $general_appearance['wpsc_text_color']?> !important; font-weight: 500;"><?php echo __('Remember me','supportcandy')?></p>

					</label>

					<div class="pull-right forgot-password">

							<a href="<?php echo home_url('login/?action=reset_pass')?>" style="color:<?php echo $general_appearance['wpsc_text_color']?> !important; font-weight: 500;"><?php echo __('Forgot Password?','supportcandy')?></a>

					</div>

			</div>

			<?php

				$wpsc_appearance_create_ticket  = get_option('wpsc_create_ticket');



				if($wpsc_login_captcha && $wpsc_recaptcha_type){

					?>

					<div class="col-md-12 captcha_container" style="margin-bottom:10px;margin-left:0px; display:flex; background-color:<?php echo $wpsc_appearance_create_ticket['wpsc_captcha_bg_color']?> !important;color:<?php echo $wpsc_appearance_create_ticket['wpsc_captcha_text_color']?> !important;">

						<div style="width:25px;">

							<input type="checkbox" onchange ="get_captcha_code(this);" id="wpsc_login_captcha_check" class="wpsc_checkbox" value="1">

							<img id="captcha_wait" style="width:16px;display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif'?>" alt="">

						</div>

						<div style=""><?php _e("I'm not a robot",'supportcandy')?></div>

					</div>

					<input type="hidden" id="captcha_code" name = "captcha_code" value="">

					<?php

				}elseif($wpsc_login_captcha && !$wpsc_recaptcha_type){

					$wpsc_get_site_key = get_option('wpsc_get_site_key');

					?>

					<div class="col-sm-12" style="margin-bottom:10px;margin-left:0px;display:flex;padding:0px;">

						<div style="width:25px;">

							<div class="g-recaptcha" data-sitekey=<?php echo $wpsc_get_site_key ?>></div>

						</div>

					</div>

					<?php

				}

			?>

			<input type="hidden" name="action" value="wpsc_tickets" />

			<input type="hidden" name="setting_action" value="set_user_login" />

			<input type="hidden" name="nonce" value="<?php echo wp_create_nonce()?>" />

			

     	   <button id="wpsc_sign_in_btn" class="btn btn-lg btn-block" type="submit" ><?php _e('Sign In','supportcandy')?></button>

			





		

		</form>
		<div class=" col-md-12 p-0 mt-2">

     	<div class="dontsignup">Don't have one? <a href="<?php echo home_url(); ?>/registration-supplier/?level=4" >Create an account </a></div>

     </div>
		

	<?php 		

} else {	

		$support_page_id  = get_option('wpsc_support_page_id');

		$support_page_url = get_permalink($support_page_id);

		

		echo $login_url = wp_login_url($support_page_url);

		if( $enable_login_settings == '3' ) {

			if (!preg_match("~^(?:f|ht)tps?://~i", $custom_login_url)) {				 

				$login_url = "http://" . $custom_login_url;					 

			} else {

				$login_url = $custom_login_url;

			}

		}

		

		?>	

		<input class="btn btn-lg btn-block" type="button" id="wpsc_login_link" onclick="window.location.href='<?php echo $login_url; ?>'" value="<?php _e('Sign In','supportcandy');?>" style="margin-top:80px; margin-bottom: 5px; <?php echo $signin_button_css ?>"/>

		<?php

}



if(get_option('wpsc_user_registration')){

	$enable_user_registration_settings =get_option('wpsc_user_registration_method');

	$wpsc_custom_registration_url=get_option('wpsc_custom_registration_url');

	if($enable_user_registration_settings == '1'){	

	?>

		<button  class="btn btn-lg btn-block" onclick="wpsc_signup_user();" type="button" style="background-color:<?php echo $wpsc_appearance_login_form['wpsc_register_now_button_bg_color']?> !important; color:<?php echo $wpsc_appearance_login_form['wpsc_register_now_text_color']?> !important;border-color:<?php echo $wpsc_appearance_login_form['wpsc_register_now_button_border_color']?> !important;"><?php _e('Register Now','supportcandy')?></button>

	<?php 

	} else {

		$registration_url = wp_registration_url();

		if( $enable_user_registration_settings == '3' ) {

			if (!preg_match("~^(?:f|ht)tps?://~i", $wpsc_custom_registration_url)) {				 

				$registration_url = "http://" . $wpsc_custom_registration_url;					 

			} else {

				$registration_url = $wpsc_custom_registration_url;

			}

		}

	?>

		<a href="<?php echo $registration_url; ?>" class="btn btn-lg btn-block" style="background-color:<?php echo $wpsc_appearance_login_form['wpsc_register_now_button_bg_color']?> !important; color:<?php echo $wpsc_appearance_login_form['wpsc_register_now_text_color']?> !important;border-color:<?php echo $wpsc_appearance_login_form['wpsc_register_now_button_border_color']?> !important;"><?php _e('Register Now','supportcandy')?></a>

	<?php	

	}



}

do_action('wpsc_after_signin_module');

?>	

<?php 

$wpsc_allow_to_create_ticket = get_option('wpsc_allow_to_create_ticket');

if( in_array('guest', $wpsc_allow_to_create_ticket) ):?>



	<button id="wpsc_login_continue_as_guest" class="btn btn-lg btn-block" onclick="wpsc_get_create_ticket();" type="button" style="background-color:<?php echo $wpsc_appearance_login_form['wpsc_continue_as_guest_button_bg_color']?> !important; color:<?php echo $wpsc_appearance_login_form['wpsc_continue_as_guest_button_text_color']?> !important; border-color:<?php echo $wpsc_appearance_login_form['wpsc_continue_as_guest_button_border_color']?> !important;"><?php _e('Continue as Guest','supportcandy')?></button>

<?php 

endif;?>

</div>



</div>



</div>

<div class="row mt-3"> 

<div class="col-md-12 pt-2 pb-2">



		

<div class="dontsignup-p">

Beta version, any future upgrade included for free and the price will never change for you!

For your client Bozzup is free (he can approve, reject or ask for modification of the mockup)

One dashboard for all your orders! 

Automatically your dashboard will be updated</div>

</div>

</div>



<div class="row mt-3 pricing-box-main">

<!-- <div class="col-12">	



<p class="card-required">Try it free for 15 days. No credit card required.</p>

</div> -->





<div class="col-md-3">

	<div  class="pricing-box1"  style="background:url(<?php echo plugin_dir_url('/').'supportcandy/asset/images/pricing.png'; ?>);">

		<h3>Smart Pricing for You!</h3>

	</div>
   
</div>

<?php

  $args = array(  

        'post_type' => 'plan',

        'post_status' => 'publish',

        'posts_per_page' => 2, 

        'orderby' => 'post_date', 

        'order' => 'DESC', 

    );



    $loop = new WP_Query( $args );



    if ( $loop->have_posts() ):

    	$count =2;

    while ( $loop->have_posts() ) : $loop->the_post(); 

      

       

       

  

 ?>

<div class="col-md-3 ">

	<div class="pricing-box<?php echo $count; ?>">

	<h3 class="text-center"><?php the_title(); ?></h3>

	<!-- <h5>What you'll get</h5> -->



	<?php if( have_rows('feature_name') ): ?>



    <ul>



    <?php while( have_rows('feature_name') ): the_row(); ?>



        <li><?php the_sub_field('feature_list'); ?></li>



    <?php endwhile; ?>



    </ul>



<?php endif;



 $title = get_the_title();



if($title == 'Monthly Plan'){



	$level = 1;

	$planname = 'Monthly';



}elseif($title == 'Yearly Plan'){



  $level = 2;
  $planname = 'Yearly';

}/*elseif($title == 'One Day Plan'){



  $level = 3;
  $planname = 'Day';

}
*/


?>

	

<p><strong>€<?php the_field('price'); echo '/'.$planname;?></strong></p> 

<a href="<?php echo home_url(); ?>/registration-supplier/?level=<?php echo $level; ?>" class="btn">Choose</a>



</div>

</div>

 <?php $count++; endwhile; endif;



    wp_reset_postdata(); ?>


<!-- <a href="<?php echo home_url(); ?>/registration-supplier/?level=4" class="btn">Choose</a> -->
<div class="col-md-3 pagesfooter">





<div class="col-md-12 addressmain p-0">

	<?php wp_nav_menu(array(
		'menu' => 'homepage menu',
		'add_li_class'  => 'list-inline-item',
    'container' => false 
)); ?>

</div>

<!--<div class="col-md-12 copyrightmain p-0">

  <p class="copyright">© 2021 BozzUp Francesco De Leo. All Rights Reserved</p>

<div class="col-md-12 addressmain">

	<h3 class="text-center">Address</h3>

	<address>

  <strong>FRANCESCO DE LEO</strong><br>

  TRIQ SANTA MARTA, RABAT<br>

  GOZO, MALTA, VCT2551<br>

  <strong><abbr title="VAT NO">VAT NO:</abbr> MT26129919</strong>

</address>

</div> 





</div>-->

</div> 





</div>
<div class="col-md-12 mt-3"><p style="color: #478E00 ">Get 2 months free when you purchase a one-year plan.</p></div>



  <div class="row">

  	<div class="col-md-12">

  		<h3>Features and Price plans</h3>
  		<p><strong>Are you a supplier of customised promotional products?</strong></p>
  		<p>Do you have mockups your customers have to approve?</p>

  		<strong>With Bozzup you can easily manage, organise and keep under control the review and approval of your customers’ mockups with just one glance and a few clicks!</strong>

  	</div>
  	<div class="col-md-12 howbozzup">
  	  <h3 class="bozzupheading">How Bozzup Works</h3>
  	  <h4>4 simple steps</h4>
  	  <ol>
  	  	<li>Create a customer.</li>
  	  	<li>Upload the mockup to be approved.</li>
  	  	<li>Send the mockup to customer.</li>
  	  	<li>Wait! You’ll receive a message when your customer approves, rejects, or asks for a modification and your dashboard is automatically updated.</li>  	  
  	  </ol>

  	  <h3 class="text-center bozzupfunctions">Bozzup functions better than a software of a big company, at a much smaller price!</h3>
  	  <h4>Save time, avoid losing information</h4>
  	  <ul>
  	  	<li>Save a lot of time to manage all your customers’ orders and mockup information.</li>
  	  	<li>Stop wasting time to find customer’s information about the mockup to realise in different places: email, whatsapp, skype, messenger, calls, etc</li>
  	  	<li>Stop losing information about the mockups to make or getting frustrated looking for them in a lot of emails, messages your customers sent to you.</li>
  	  	<li>Never lose track of customers who still need to review and approve the mockups you sent. Bozzup will automatically send a reminder to your customers if they haven’t reviewed their mockups by any custom deadline you set! (coming soon)</li>  	  
  	  </ul>
  	  <h3 class="bozzupheading">Easy-to-use, all-in-one dashboard</h3>
  	  <h5 class="text-center getpeace">Get peace of mind and eliminate stress by keeping and managing <br>all the information in one place:<br>BozzUp – The Mockup Approval R-evolution!</h5>

  	  <div class="customerreceiving">If you are a customer receiving mockups to review from your suppliers: Bozzup is free for you.</div>


  	</div>
  	

  </div>
  <div class="row freedashboard">
  	<div class="col-md-6 ">
  		<strong>A free dashboard to review the mockups of your orders of promotional products</strong>
  		<ul>
  			<li>You receive a message when a supplier sends to you a mockup to review and approve.</li>
  			<li>You can approve, reject or request a modification of the mockup.</li>
  			<li>Your dashboard will be automatically updated.</li>
  			<li>Bozzup is a well-organised archive of all your orders from your suppliers.</li>
  			<li>You can easily find a supplier, an order and the approved mockup.</li>
  			<li>If your suppliers use Bozzup you can see all orders of different suppliers on your dashboard, so ask to your suppliers to sign-up! </li>
  			<li>With the Customer Pro Version you can also to re-order your products to your supplier with one click! (coming soon).</li>
  		</ul>
  	</div>
  	<div class="col-md-6">
  	<strong>Features / Roadmap:</strong>
  		<ul>
  			<li>Automatic sending of reminders (coming soon)</li>
  			<li>Import contacts (coming soon)</li>
  			<li>Import orders (coming soon)</li>
  			<li>Add your own branding (coming soon)</li>
  			<li>Google integration: Calendar, Drive (coming soon)</li>
  			<li>Logos archive (coming soon)</li>
  			<li>Log in through Google or Facebook (coming soon)</li>
  			<li>API integration (coming soon)</li>
  			<li>Upload mockups to dashboard by email (coming soon)</li>
  			<li>iOS & Android support (coming soon)</li>
  		</ul>
  	</div>
  </div>
  <div class="row">
  	
  		<div class="col-md-12 text-center mt-4">
			©2021 BozzUp Francesco De Leo. All Rights Reserved - Trq Santa Marta, Rabat Gozo, Malta VCT2551 - Vat No: MT26129919
			</div>
			<div class="col-md-12 text-center mt-2">
				<a href="/privacy-policy" class="btn privacy" role="button">Privacy Policy</a>

			<a href="/cookie-policy" class="btn privacy" role="button">Cookie Policy</a>
			</div>
  </div>
  </div>

<?php do_action('wpsc_after_guest_module'); ?>



<script>

<?php 

if($enable_login_settings=='1' && $wpsc_login_captcha && $wpsc_recaptcha_type) {

	?>

	function get_captcha_code(e){

		jQuery(e).hide();

		jQuery('#captcha_wait').show();

		var data = {

			action: 'wpsc_tickets',

			setting_action : 'get_captcha_code'

		};

		jQuery.post(wpsc_admin.ajax_url, data, function(response) {

			jQuery('#captcha_code').val(response);

			jQuery('#captcha_wait').hide();

			jQuery(e).show();

			jQuery(e).prop('disabled',true);

		});

	}

	<?php

}

?>

function wpsc_sign_in(){

	<?php

		if($wpsc_login_captcha && $wpsc_recaptcha_type){

			?>

			if (jQuery('#captcha_code').val().trim().length==0) {

				alert("<?php _e('Please confirm you are not a robot!','supportcandy')?>");

				validation = false;

				return;

			}

			<?php

		}elseif($wpsc_login_captcha && !$wpsc_recaptcha_type){

			?>

			var recaptcha = jQuery("#g-recaptcha-response").val();

			if (recaptcha === "") {

				alert("<?php _e('Please confirm you are not a robot!','supportcandy')?>");

				validation = false;

				return;

			}

			<?php

		}

	?>

  var dataform = new FormData(jQuery('#frm_wpsc_sign_in')[0]);

  jQuery('#frm_wpsc_sign_in').find('input,button').attr('disabled',true);

  jQuery.ajax({

    url: wpsc_admin.ajax_url,

    type: 'POST',

    data: dataform,

    processData: false,

    contentType: false

  })

  .done(function (response_str) {

    var response = JSON.parse(response_str);



    if (response.error == '1') {

      jQuery('#wpsc_message_login').html(response.message);

      jQuery('#wpsc_message_login').attr('class','bg-danger').slideDown('fast',function(){});

      jQuery('#frm_wpsc_sign_in').find('input,button').attr('disabled',false);

      jQuery('#frm_wpsc_sign_in').find('#inputPassword').val('');

	  setTimeout(function(){ jQuery('#wpsc_message_login').slideUp('fast',function(){}); }, 5000);

	  jQuery("#wpsc_login_captcha_check").prop("checked", false); 

	  jQuery('#captcha_code').val('');

	  jQuery('#wpsc_login_captcha_check').prop("disabled", false);

	  grecaptcha.reset();



    }else if (response.error == '2') {

    	jQuery('#wpsc_message_login').html(response.message);

      jQuery('#wpsc_message_login').attr('class','bg-danger').slideDown('fast',function(){});

      jQuery('#frm_wpsc_sign_in').find('input,button').attr('disabled',false);

      jQuery('#frm_wpsc_sign_in').find('#inputPassword').val('');

	  setTimeout(function(){ jQuery('#wpsc_message_login').slideUp('fast',function(){}); }, 5000);

	  jQuery("#wpsc_login_captcha_check").prop("checked", false); 

	  jQuery('#captcha_code').val('');

	  jQuery('#wpsc_login_captcha_check').prop("disabled", false);

	  location.reload(true);

	 

    }else {

      jQuery('#wpsc_message_login').html(response.message);

      jQuery('#wpsc_message_login').attr('class','bg-success').slideDown('fast',function(){});

      location.reload(true);

    }

  });

}




function wpsc_user_type(myvalue) {
   
    var selectedval = myvalue.value;

    if(myvalue.value == 'supplier'){

    	jQuery('#usertypemesg').text('I am a supplier, I have mockups for clients to approve.');
    }else if(myvalue.value == 'subscriber'){
    	
    	jQuery('#usertypemesg').text('I am a customer, I have mockups to approve from my suppliers.');
    }
    
}
setTimeout(function(){ jQuery('.confirmemail').remove(); }, 5000);
</script>

<?php if (!$wpsc_recaptcha_type && $wpsc_login_captcha): ?>

	 <script src='https://www.google.com/recaptcha/api.js'></script>

 <?php endif; ?>