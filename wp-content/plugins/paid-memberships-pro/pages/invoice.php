<div class="<?php echo pmpro_get_element_class( 'pmpro_invoice_wrap' ); ?>">
	<?php
	global $wpdb, $pmpro_invoice, $pmpro_msg, $pmpro_msgt, $current_user;

	if($pmpro_msg)
	{
	?>
	<div class="<?php echo pmpro_get_element_class( 'pmpro_message ' . $pmpro_msgt, $pmpro_msgt ); ?>"><?php echo $pmpro_msg?></div>
	<?php
	}
?>

<?php
	if($pmpro_invoice)
	{
		?>
		<?php
			$pmpro_invoice->getUser();
			$pmpro_invoice->getMembershipLevel();
		?>

<div class="row m-0">
    <div class="col-sm-6 p-0">
    	<h3 style="margin:20px 0 30px 0; "><?php printf(__('Invoice #%s', 'paid-memberships-pro' ), $pmpro_invoice->code);?></h3>
    </div>
    <div class="col-sm-6">
    	<a class="<?php echo pmpro_get_element_class( 'pmpro_a-print' ); ?>" href="javascript:window.print()"><?php _e('Print', 'paid-memberships-pro' ); ?></a>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
    	<?php if(!empty($pmpro_invoice->billing->street)) { ?>
				<div class="<?php echo pmpro_get_element_class( 'pmpro_invoice-billing-address' ); ?>">
					<strong><?php _e('Bill To', 'paid-memberships-pro' );?></strong>
					<p class="mb-0">
						<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_name1' ); ?>"><?php echo $pmpro_invoice->billing->name; ?></span><br>
						<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_company1' ); ?>"><?php echo get_user_meta($current_user->ID, 'company', true); ?></span><br>
						<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_phone1' ); ?>"><?php echo formatPhone($pmpro_invoice->billing->phone); ?></span><br>
						<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_street1' ); ?>"><?php echo $pmpro_invoice->billing->street; ?></span>

						<?php if($pmpro_invoice->billing->city && $pmpro_invoice->billing->state) { ?>
							<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_zip1' ); ?>"><?php echo $pmpro_invoice->billing->zip; ?></span><br>
							<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_city1' ); ?>"><?php echo $pmpro_invoice->billing->city; ?></span><br>
							<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_state1' ); ?>"><?php echo $pmpro_invoice->billing->state; ?></span><br>
							
							<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_country1' ); ?>">

								<?php 

								global $pmpro_countries;

								

								foreach($pmpro_countries as $abbr => $country) {
									
									if($abbr == $pmpro_invoice->billing->country) {echo $country; }
								}

								//echo $pmpro_invoice->billing->country; 



							?></span><br>
						<?php } ?>
						<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_vatno1' ); ?>"><?php echo get_user_meta($current_user->ID, 'pmpro_bvatno', true); ?></span>
					</p>
				</div> <!-- end pmpro_invoice-billing-address -->
			<?php } ?>
    </div>
    </div>
    <hr />
    <div class="row">
    <div class="col-sm-12">
    	<!-- <h4><?php printf(__('Invoice #%s on %s', 'paid-memberships-pro' ), $pmpro_invoice->code, date_i18n(date( 'Y-m-d', $pmpro_invoice->getTimestamp())));?></h4> -->
		
		<ul style="padding-left:20px;">
			<li><strong><?php _e('Invoice Id', 'paid-memberships-pro' );?>:</strong> #<?php echo $pmpro_invoice->code; ?></li>
			<li><strong><?php _e('Invoice Date', 'paid-memberships-pro' );?>:</strong> <?php echo  date_i18n(date( 'Y-m-d', $pmpro_invoice->getTimestamp())); ?></li>
			<?php do_action("pmpro_invoice_bullets_top", $pmpro_invoice); ?>
			<!-- <li><strong><?php _e('Account', 'paid-memberships-pro' );?>:</strong> <?php echo $pmpro_invoice->user->display_name?> (<?php echo $pmpro_invoice->user->user_email?>)</li> -->
			<li><strong><?php _e('Plan', 'paid-memberships-pro' );?>:</strong> <?php echo $pmpro_invoice->membership_level->name?></li>
			<?php if ( ! empty( $pmpro_invoice->status ) ) { ?>
				<li><strong><?php _e('Status', 'paid-memberships-pro' ); ?>:</strong>
				<?php
					if ( in_array( $pmpro_invoice->status, array( '', 'success', 'cancelled' ) ) ) {
						$display_status = __( 'Paid', 'paid-memberships-pro' );
					} else {
						$display_status = ucwords( $pmpro_invoice->status );
					}
					esc_html_e( $display_status );
				?>
				</li>
			<?php } ?>
			<?php if($pmpro_invoice->getDiscountCode()) { ?>
				<li><strong><?php _e('Discount Code', 'paid-memberships-pro' );?>:</strong> <?php echo $pmpro_invoice->discount_code->code?></li>
			<?php } ?>
			<?php do_action("pmpro_invoice_bullets_bottom", $pmpro_invoice); ?>
		</ul>
    </div>
</div>





		
		

		<?php
			// Check instructions
			if ( $pmpro_invoice->gateway == "check" && ! pmpro_isLevelFree( $pmpro_invoice->membership_level ) ) {
				echo '<div class="' . pmpro_get_element_class( 'pmpro_payment_instructions' ) . '">' . wpautop( wp_unslash( pmpro_getOption("instructions") ) ) . '</div>';
			}
		?>

		
		<div class="<?php echo pmpro_get_element_class( 'pmpro_invoice_details' ); ?>">
		<div class="row">
	    <div class="col-sm-6">
	    	<?php if ( ! empty( $pmpro_invoice->accountnumber ) || ! empty( $pmpro_invoice->payment_type ) ) { ?>
				<div class="<?php echo pmpro_get_element_class( 'pmpro_invoice-payment-method' ); ?>">
					<strong><?php _e('Payment Method', 'paid-memberships-pro' );?></strong>
					<?php if($pmpro_invoice->accountnumber) { ?>
						<p><?php echo ucwords( $pmpro_invoice->cardtype ); ?> <?php _e('ending in', 'paid-memberships-pro' );?> <?php echo last4($pmpro_invoice->accountnumber)?>
						<br />
						<?php _e('Expiration', 'paid-memberships-pro' );?>: <?php echo $pmpro_invoice->expirationmonth?>/<?php echo $pmpro_invoice->expirationyear?></p>
					<?php } else { ?>
						<p><?php echo $pmpro_invoice->payment_type; ?></p>
					<?php } ?>
				</div> <!-- end pmpro_invoice-payment-method -->
			<?php } ?>
	    </div>
	    <div class="col-sm-6">
	    	
			<div class="<?php echo pmpro_get_element_class( 'pmpro_invoice-total' ); ?>">
				<strong><?php _e('Total Billed', 'paid-memberships-pro' );?></strong>
				<p>
					<?php
						if ( (float)$pmpro_invoice->total > 0 ) {
							echo pmpro_get_price_parts( $pmpro_invoice, 'span' );
						} else {
							echo pmpro_escape_price( pmpro_formatPrice(0) );
						}
					?>
				</p>
			</div> <!-- end pmpro_invoice-total -->
	    </div>
	    </div>	

			

		</div> <!-- end pmpro_invoice_details -->
		<hr />
		<div class="row">
	    <div class="col-sm-12 text-center">
	    	 <a href="/">
		    	<img class="invoicelogo" src = "<?php echo get_stylesheet_directory_uri().'/images/logo.png'; ?>" alt="logo"/ width="300px">
		    </a><br><br>
		    <p> Bozzup by Francesco De Leo<br> 
Address: Triq Santa Marta, Santa Marta Court Fl.3<br>  
Zip code: VCT 2251 City: Ir-Rabat Country: Ghawdex, Malta <br> 
VAT N. MT26129919</p>
	    </div>
	    </div>
		<?php
	}
	else
	{
		//Show all invoices for user if no invoice ID is passed
		$invoices = $wpdb->get_results("SELECT o.*, UNIX_TIMESTAMP(CONVERT_TZ(o.timestamp, '+00:00', @@global.time_zone)) as timestamp, l.name as membership_level_name FROM $wpdb->pmpro_membership_orders o LEFT JOIN $wpdb->pmpro_membership_levels l ON o.membership_id = l.id WHERE o.user_id = '$current_user->ID' AND o.status NOT IN('review', 'token', 'error') ORDER BY timestamp DESC");
		if($invoices)
		{
			?>
			<h3 style="margin:20px 0 30px 0; "><?php _e("Past Invoices", 'paid-memberships-pro' );?></h3>
			<table id="pmpro_invoices_table" class="<?php echo pmpro_get_element_class( 'pmpro_table pmpro_invoice', 'pmpro_invoices_table' ); ?>" width="100%" cellpadding="0" cellspacing="0" border="0">
			<thead>
				<tr>
					<th><?php _e('Date', 'paid-memberships-pro' ); ?></th>
					<th><?php _e('Invoice #', 'paid-memberships-pro' ); ?></th>
					<th><?php _e('Level', 'paid-memberships-pro' ); ?></th>
					<th><?php _e('Total Billed', 'paid-memberships-pro' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
				foreach($invoices as $invoice)
				{
					?>
					<tr>
						<td><a href="<?php echo pmpro_url("invoice", "?invoice=" . $invoice->code)?>"><?php echo date_i18n( get_option("date_format"), strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s', $invoice->timestamp ) ) ) )?></a></td>
						<td><a href="<?php echo pmpro_url("invoice", "?invoice=" . $invoice->code)?>"><?php echo $invoice->code; ?></a></td>
						<td><?php echo $invoice->membership_level_name;?></td>
						<td><?php echo pmpro_formatPrice($invoice->total);?></td>
					</tr>
					<?php
				}
			?>
			</tbody>
			</table>
			<?php
		}
		else
		{
			?>
			<p><?php _e('No invoices found.', 'paid-memberships-pro' );?></p>
			<?php
		}
	}
?>
<!-- <p class="<?php echo pmpro_get_element_class( 'pmpro_actions_nav' ); ?>">
	<span class="<?php echo pmpro_get_element_class( 'pmpro_actions_nav-right' ); ?>"><a href="<?php echo pmpro_url("account")?>"><?php _e('View Your Membership Account &rarr;', 'paid-memberships-pro' );?></a></span>
	<?php if ( $pmpro_invoice ) { ?>
		<span class="<?php echo pmpro_get_element_class( 'pmpro_actions_nav-left' ); ?>"><a href="<?php echo pmpro_url("invoice")?>"><?php _e('&larr; View All Invoices', 'paid-memberships-pro' );?></a></span>
	<?php } ?>
</p> --> <!-- end pmpro_actions_nav -->
</div> <!-- end pmpro_invoice_wrap -->
