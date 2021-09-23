





<div class="<?php echo pmpro_get_element_class( 'pmpro_confirmation_wrap' ); ?>">
<?php
	global $wpdb, $current_user, $pmpro_invoice, $pmpro_msg, $pmpro_msgt;

	if($pmpro_msg)
	{
	?>
		<div class="<?php echo pmpro_get_element_class( 'pmpro_message ' . $pmpro_msgt, $pmpro_msgt ); ?>"><?php echo wp_kses_post( $pmpro_msg );?></div>
	<?php
	} ?>

	 <div class="row d-flex justify-content-center">

    <div class="col-sm-10 text-center invoice-message">
    	
    	<img src="<?php echo plugin_dir_url( __DIR__ ) . 'images/Tickgif.gif'; ?>" width="120px"> 
    	<h3>Membership Confirmation</h3>

	<?php if(empty($current_user->membership_level))
		$confirmation_message = "<p>" . __('Your payment has been submitted. Your membership will be activated shortly.', 'paid-memberships-pro' ) . "</p>";
	else
		$confirmation_message = "<p>" . sprintf(__('Thank you for your membership to %s. Your %s membership is now active.', 'paid-memberships-pro' ), get_bloginfo("name"), $current_user->membership_level->name) . "</p>";

	//confirmation message for this level
	$level_message = $wpdb->get_var("SELECT l.confirmation FROM $wpdb->pmpro_membership_levels l LEFT JOIN $wpdb->pmpro_memberships_users mu ON l.id = mu.membership_id WHERE mu.status = 'active' AND mu.user_id = '" . $current_user->ID . "' LIMIT 1");
	if(!empty($level_message))
		$confirmation_message .= "\n" . stripslashes($level_message) . "\n";
?>

<?php if(!empty($pmpro_invoice) && !empty($pmpro_invoice->id)) { ?>

	<?php
		
		$pmpro_invoice->getUser();

		$pmpro_invoice->getMembershipLevel();

		$confirmation_message .= "<p>" . sprintf(__('Below are details about your membership account. A welcome email with details of your membership has been sent to %s.', 'paid-memberships-pro' ), $pmpro_invoice->user->user_email) . " You can also download your Invoice from the <a href=".home_url('/membership-account/').">settings</a> section</p>";

		// Check instructions
		if ( $pmpro_invoice->gateway == "check" && ! pmpro_isLevelFree( $pmpro_invoice->membership_level ) ) {
			$confirmation_message .= '<div class="' . pmpro_get_element_class( 'pmpro_payment_instructions' ) . '">' . wpautop( wp_unslash( pmpro_getOption("instructions") ) ) . '</div>';
		}

		/**
		 * All devs to filter the confirmation message.
		 * We also have a function in includes/filters.php that applies the the_content filters to this message.
		 * @param string $confirmation_message The confirmation message.
		 * @param object $pmpro_invoice The PMPro Invoice/Order object.
		 */
		$confirmation_message = apply_filters("pmpro_confirmation_message", $confirmation_message, $pmpro_invoice);

		echo wp_kses_post( $confirmation_message );
	?>
	 </div>
    
  </div>
<div class="row d-flex justify-content-center">

    <div class="col-sm-10 mt-4">
    	<div class="col-sm-12 mt-4">	<a class="<?php echo pmpro_get_element_class( 'pmpro_a-print' ); ?> btn" href="<?php echo home_url('/'); ?>membership-account/membership-invoice/?invoice=<?php echo $pmpro_invoice->code; ?>"><?php _e('Download Invoice', 'paid-memberships-pro' );?></a>
  	 	
  	 </div>
  	 <div class="clearfix"></div>
  <div class="col-sm-6 float-left invoice-left">
  	 	
  	 	
  	 	<h4><?php printf(__('Invoice #%s', 'paid-memberships-pro' ), $pmpro_invoice->code);?></h4>
  	 	<p><?php printf(__('on %s', 'paid-memberships-pro' ), date( 'Y-m-d', $pmpro_invoice->getTimestamp() ));?></p>

	
	<div class="account-left">
	
		<ul>
			<?php do_action("pmpro_invoice_bullets_top", $pmpro_invoice); ?>
			<li><strong><?php _e('Account', 'paid-memberships-pro' );?></strong> <span class="account-name"><?php echo esc_html( $current_user->display_name );?> <?php //echo esc_html( $current_user->user_email );?></span></li>
			<li class="account-name"><h4 ><?php _e(__('Membership Card', 'paid-memberships-pro' ))?></h4></li>
			<li><strong><?php _e('Membership Level', 'paid-memberships-pro' );?></strong> <span class="account-name"><?php echo esc_html( $current_user->membership_level->name);?></span></li>
			<?php if($current_user->membership_level->enddate) { ?>
				<li><strong><?php _e('Membership Expires', 'paid-memberships-pro' );?></strong> <span class="account-name"><?php echo date_i18n(get_option('date_format'), $current_user->membership_level->enddate)?></span></li>
			<?php } ?>
			<?php if($pmpro_invoice->getDiscountCode()) { ?>
				<li><strong><?php _e('Discount Code', 'paid-memberships-pro' );?></strong> <?php echo esc_html( $pmpro_invoice->discount_code->code );?></li>
			<?php } ?>
			<?php do_action("pmpro_invoice_bullets_bottom", $pmpro_invoice); ?>
		</ul>
   </div>

 
</div><!-- col-sm-6 -->
	

	<div class="col-sm-6 float-left invoice-right <?php echo pmpro_get_element_class( 'pmpro_invoice_details' ); ?>">
		<?php if(!empty($pmpro_invoice->billing->street)) { ?>
			<div class="<?php echo pmpro_get_element_class( 'pmpro_invoice-billing-address' ); ?>" style="display: none;">
				<strong><?php _e('Billing Address', 'paid-memberships-pro' );?></strong>
				<p>
					<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_name1' ); ?>"><?php echo $pmpro_invoice->billing->name; ?></span><br>
					<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_company1' ); ?>"><?php echo get_user_meta($current_user->ID, 'company', true); ?></span><br>
					<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_phone1' ); ?>"><?php echo formatPhone($pmpro_invoice->billing->phone); ?></span><br>

					<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_street1' ); ?>"><?php echo $pmpro_invoice->billing->street; ?></span><br>
					<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_zip1' ); ?>"><?php echo $pmpro_invoice->billing->zip; ?></span><br>
					<?php if ( $pmpro_invoice->billing->city && $pmpro_invoice->billing->state ) { ?>
						<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_city1' ); ?>"><?php echo $pmpro_invoice->billing->city; ?></span>
						<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_state1' ); ?>"><?php echo $pmpro_invoice->billing->state; ?></span>						
						<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_country1' ); ?>"><?php echo $pmpro_invoice->billing->country; ?></span><br>						
					<?php } ?>
					<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_vatno1' ); ?>"><?php echo get_user_meta($current_user->ID, 'pmpro_bvatno', true);  ?></span>
				</p>
			</div> <!-- end pmpro_invoice-billing-address -->
		<?php } ?>


     
  	 
  	 
  	 	
  	  <div class="col-sm-12 payment-main mt-4">
  	 	<h4><?php _e('Payment Summary', 'paid-memberships-pro' );?></h4>
  	 	<!-- <hr /> -->
  	
  	
  	 <table width="100%" class="payment-table">
  	 		<tbody>

		<?php if ( ! empty( $pmpro_invoice->accountnumber ) || ! empty( $pmpro_invoice->payment_type ) ) { ?>
			<tr class="<?php echo pmpro_get_element_class( 'pmpro_invoice-payment-method' ); ?>">


  	 			<?php if($pmpro_invoice->accountnumber) { ?>
  	 				<td><?php _e('Payment Method', 'paid-memberships-pro' );?></td>
  	 				<td><?php echo esc_html( ucwords( $pmpro_invoice->cardtype ) ); ?> <?php _e('ending in', 'paid-memberships-pro' );?> <?php echo esc_html( last4($pmpro_invoice->accountnumber ) );?></td>
  	 			
  	 				<td><?php _e('Expiry Date', 'paid-memberships-pro' );?></td>
  	 				<td><?php echo esc_html( $pmpro_invoice->expirationmonth );?>/<?php echo esc_html( $pmpro_invoice->expirationyear );?></td>
  	 			<?php } else { ?>
					<td><?php echo esc_html( $pmpro_invoice->payment_type ); ?></td>
				<?php } ?>

  	 			
  	 			
			</tr> <!-- end pmpro_invoice-payment-method -->
		<?php } ?>

		<tr class="<?php echo pmpro_get_element_class( 'pmpro_invoice-total' ); ?>">
			<td><?php _e('Total Billed', 'paid-memberships-pro' );?></td>
			<td>
				<?php
					if ( (float)$pmpro_invoice->total > 0 ) {
						echo pmpro_get_price_parts( $pmpro_invoice, 'span' );
					} else {
						echo pmpro_escape_price( pmpro_formatPrice(0) );
					}
				?>
			</td>
		</tr> <!-- end pmpro_invoice-total -->

		</tbody>
  	 	</table>
  	 	</div>
  	 </div>

  <div class="clearfix"></div>
  <div class="col-sm-12 mt-4">
  <p class="<?php echo pmpro_get_element_class( 'pmpro_actions_nav' ); ?>">
	<?php if ( ! empty( $current_user->membership_level ) ) { ?>
		<a href="<?php echo home_url(); ?>"><?php _e( 'Go to dashboard', 'paid-memberships-pro' ); ?></a><br><br>
	<?php } else { ?>
		<?php _e( 'If your account is not activated within a few minutes, please contact the site owner.', 'paid-memberships-pro' ); ?>
	<?php } ?>
</p> <!-- end pmpro_actions_nav -->
</div>
</div>
  </div>
  </div>
	</div> <!-- end pmpro_invoice -->
	
<?php


    $_SESSION['user_type'] = 'supplier';
	}
	else
	{
		$confirmation_message .= "<p>" . sprintf(__('Below are details about your membership account. A welcome email with details of your membership has been sent to %s.', 'paid-memberships-pro' ), $current_user->user_email) . "</p>";

		/**
		 * All devs to filter the confirmation message.
		 * Documented above.
		 * We also have a function in includes/filters.php that applies the the_content filters to this message.
		 */
		$confirmation_message = apply_filters("pmpro_confirmation_message", $confirmation_message, false);

		echo wp_kses_post( $confirmation_message );
	?>

	<div class="account-left">
	<ul>
		<li><strong><?php _e('Account1', 'paid-memberships-pro' );?>:</strong> <span class="account-name"><?php echo esc_html( $current_user->display_name );?> </span></li>
		<li class="account-name"><h4 ><?php _e(__('Membership Card', 'paid-memberships-pro' ))?></h4></li>
		<li><strong><?php _e('Membership Level', 'paid-memberships-pro' );?>:</strong> <span class="account-name"><?php if(!empty($current_user->membership_level)) echo esc_html( $current_user->membership_level->name ); else _e("Pending", 'paid-memberships-pro' );?></span></li>
	</ul>


</div>
<div class="col-sm-12 mt-4 p-0">
  <p class="<?php echo pmpro_get_element_class( 'pmpro_actions_nav' ); ?>">
	<?php if ( ! empty( $current_user->membership_level ) ) { ?>
		<a href="<?php echo home_url(); ?>"><?php _e( 'Go to dashboard', 'paid-memberships-pro' ); ?></a><br><br>
	<?php } else { ?>
		<?php _e( 'If your account is not activated within a few minutes, please contact the site owner.', 'paid-memberships-pro' ); ?>
	<?php } ?>
</p> <!-- end pmpro_actions_nav -->
</div>
<?php

	
    $_SESSION['user_type'] = 'supplier';

	}
?>

</div> <!-- end pmpro_confirmation_wrap -->
