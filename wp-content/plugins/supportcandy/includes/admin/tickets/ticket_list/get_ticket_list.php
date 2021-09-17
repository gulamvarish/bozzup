<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;
if (!$current_user->ID){

	die();
}

$filter = $wpscfunction->get_current_filter();
$wpsc_appearance_ticket_list = get_option('wpsc_appearance_ticket_list');
$wpsc_appearance_modal_window = get_option('wpsc_modal_window');

// Initialize meta query



/*Added by Gulam*/

	if( $_SESSION['user_type'] == 'supplier'){

		$slug = 'agent_'.$current_user->ID;
		$termid = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}terms WHERE `slug` ='".$slug."'");
		$meta_query = array(
			'relation' => 'AND',
			array(
				'key'            => 'user',
				'value'          => $_SESSION['user_type'],
				'compare'        => '='
			),
			array(
				'key'            => 'agent_created',
				'value'          => $termid[0]->term_id,
				'compare'        => '='
			),
		);


		
		$ticketexit = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wpsc_ticket WHERE `agent_created` ='".$termid[0]->term_id."'");

	}

	if( $_SESSION['user_type'] == 'subscriber'){

		 $slug = 'agent_'.$current_user->ID;
		 $termid = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}terms WHERE `slug` ='".$slug."'");     

		$meta_query = array(
			'relation' => 'AND',		
			array(
				'key'            => 'agent_created',
				'value'          => $termid[0]->term_id,
				'compare'        => '!='
			),
		);

		$ticketexit = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wpsc_ticket WHERE `customer_email` ='".$current_user->user_email."'");
	}

	

/*Added by Gulam*/

if ( !is_multisite() || !is_super_admin($current_user->ID)) {
	// Initialie restrictions. Everyone should able to see their own tickets.
	$restrict_rules = array(
		'relation' => 'OR',
		array(
			'key'            => 'customer_email',
			'value'          => $current_user->user_email,
			'compare'        => '='
		),
		
	);




	if ($current_user->has_cap('wpsc_agent') ) {
		
		$post_per_page     = get_option('wpsc_tl_agent_no_of_tickets');
		$agent_permissions = $wpscfunction->get_current_agent_permissions();
		$current_agent_id  = $wpscfunction->get_current_user_agent_id();
		
		if(!$current_agent_id) die();
		
		if ($agent_permissions['view_unassigned'] && $filter['label']!='mine') {
			$restrict_rules[] = array(
				'key'            => 'assigned_agent',
				'value'          => 0,
				'compare'        => '='
			);
		}
		
		if ($agent_permissions['view_assigned_me']) {
			$restrict_rules[] = array(
				'key'            => 'assigned_agent',
				'value'          => $current_agent_id,
				'compare'        => '='
			);
		}
		
		if ($agent_permissions['view_assigned_others'] && $filter['label']!='mine') {
			$restrict_rules[] = array(
				'key'            => 'assigned_agent',
				'value'          => array(0,$current_agent_id),
				'compare'        => 'NOT IN'
			);
		}
		
		$restrict_rules = apply_filters('wpsc_tl_agent_restrict_rules',$restrict_rules);		
	} else {
		
		$post_per_page = get_option('wpsc_tl_customer_no_of_tickets');
		
		$restrict_rules = apply_filters('wpsc_tl_customer_restrict_rules',$restrict_rules);	
			
	}

	$wpsc_ticket_public_mode = get_option('wpsc_ticket_public_mode');

	if( !$current_user->has_cap('wpsc_agent') && $wpsc_ticket_public_mode){
		$restrict_rules[] = array(
			'key'            => 'active',
			'value'          => 1,
			'compare'        => '='
		);
	}

	

	$meta_query[] = $restrict_rules;
	
} else {
	
		$post_per_page = get_option('wpsc_tl_agent_no_of_tickets');
	
}

// Merge default filter label
if($filter['query']){
	$meta_query = array_merge($meta_query, $filter['query']);
}

// Select offset for page number
$offset = ($filter['page']-1)*$post_per_page;


?>

<form id="frm_additional_filters" action="javascript:wpsc_set_custom_filter();" method="post">
	<div class="wpsc_ticket_search_box d-flex align-items-center" style="margin-bottom:20px;padding:0;">
		<!-- <div class="float-right" style="width: 100px;"> -->
		<input type="text" id="wpsc_ticket_search" class="form-control w-auto" name="custom_filter[s]" value="<?php echo trim($filter['custom_filter']['s'])?>" autocomplete="off" placeholder="<?php _e('FILTERS...','supportcandy')?>" onclick="show_custom_filters();">

		<!-- <i class="fa fa-search wpsc_search_btn wpsc_search_btn_sarch"></i> -->
		<i class="fa fa-caret-down wpsc_search_btn wpsc_search_btn_filter" onclick="show_custom_filters();"></i>
		<!-- </div> -->
		<input type="submit" name="submit" style="position: fixed; top: -1000px;" />
		<div class="wpsp_custom_filter_container" style="display:none;">
			
			<div class="row wpsp_filter_body" style="">
					<?php
					if ($current_user->has_cap('wpsc_agent') && $_SESSION['user_type'] =="supplier") {


						$fields = get_terms([
							'taxonomy'   => 'wpsc_ticket_custom_fields',
							'hide_empty' => false,
							'orderby'    => 'meta_value_num',
							'meta_key'	 => 'wpsc_filter_agent_load_order',
							'order'    	 => 'DESC', //Edit By gulam ASC to DESC
							'meta_query' => array(
								'relation' => 'AND',
						    array(
						      'key'       => 'wpsc_allow_ticket_filter',
						      'value'     => '1',
						      'compare'   => '='
						    ),
						    array(
						      'key'       => 'wpsc_agent_ticket_filter_status',
						      'value'     => '1',
						      'compare'   => '='
						    )
							),
						]);
					}
					else {

						
						$fields = get_terms([
							'taxonomy'   => 'wpsc_ticket_custom_fields',
							'hide_empty' => false,
							'orderby'    => 'meta_value_num',
							'meta_key'	 => 'wpsc_filter_customer_load_order',
							'order'    	 => 'DESC', //Edit By gulam ASC to DESC
							'meta_query' => array(
								'relation' => 'AND',
						    array(
						      'key'       => 'wpsc_allow_ticket_filter',
						      'value'     => '1',
						      'compare'   => '='
						    ),
						    array(
						      'key'       => 'wpsc_customer_ticket_filter_status',
						      'value'     => '1',
						      'compare'   => '='
						    )
							),
						]);
					}
					foreach ( $fields as $field ){




						 $label       = get_term_meta( $field->term_id, 'wpsc_tf_label', true);
						$filter_type = get_term_meta( $field->term_id, 'wpsc_ticket_filter_type', true);
						$wpsc_tf_type = get_term_meta( $field->term_id,'wpsc_tf_type', true);

						/*if($label == 'Customer' && !$current_user->has_cap('wpsc_agent') )
							continue;*/
						

						if($label == 'Status'){
							$label = 'Status';
						}


						   if ($filter_type=='string' || $filter_type=='number') {
							if($field->slug == 'ticket_id'){
								$field->slug = 'id';
							}
							?>
							<div id="tf_<?php echo $field->slug?>" class="form-group col-sm-12">
								<label><?php echo __(htmlentities($label),'supportcandy');?></label>
								<input type="text" data-field="<?php echo $field->slug?>" class="form-control wpsc_search_autocomplete" placeholder="<?php _e('Search...','supportcandy')?>">

								<ul class="wpsp_filter_display_container">
								
									<?php if(isset($filter['custom_filter'][$field->slug])):
										if(isset($filter['custom_filter'][$field->slug]) && apply_filters('wpsc_add_ticket_meta',true,$field->slug)){

											
											$meta_query[] = array(
												'key'     => $field->slug,
												'value'   => $filter['custom_filter'][$field->slug],
												'compare' => 'IN'
											);
										}else {

											$meta_query = apply_filters('wpsc_get_tickets_meta', $meta_query,$field->slug,$filter['custom_filter'][$field->slug]);
										}
									?>
									<?php foreach ($filter['custom_filter'][$field->slug] as $key => $value):?>
										<li class="wpsp_filter_display_element">
											<div class="flex-container">
												<div class="wpsp_filter_display_text">
													<?php echo $wpscfunction->get_tf_value_filter_label($field->slug,$value)?>
													<input type="hidden" name="custom_filter[<?php echo $field->slug?>][]" value="<?php echo $value?>">
												</div>
												<div class="wpsp_filter_display_remove" onclick="wpsc_remove_filter(this);">
													<i class="fa fa-times"></i>
												</div>
											</div>
										</li>
									<?php endforeach;?>
								<?php endif;?>								
								</ul>
							</div>
							<?php
						} elseif($filter_type=='datetime') {
							if( isset($filter['custom_filter'][$field->slug]['from']) && $filter['custom_filter'][$field->slug]['from'] && isset($filter['custom_filter'][$field->slug]['to']) && $filter['custom_filter'][$field->slug]['to'] ){
								$meta_query[] = array(
									'key'     => $field->slug,
									'value'   => array( 
										$filter['custom_filter'][$field->slug]['from'],
 									  $filter['custom_filter'][$field->slug]['to'],
									),
									'compare' => 'BETWEEN',
									'type' => 'DATE',
								);
							}
							
							?>
							<div class="row form-group">
								<label style="width:100%;padding-left:15px;"><?php echo __(htmlentities($label),'supportcandy')?></label>
								<div class="col-sm-6">
									<input type="text" autocomplete="off" class="form-control wpsc_datetime" name="custom_filter[<?php echo $field->slug?>][from]" value="<?php echo isset($filter['custom_filter'][$field->slug]['from'])?$filter['custom_filter'][$field->slug]['from']:'';?>" placeholder="<?php _e('From','supportcandy')?>">
								</div>
								<div class="col-sm-6">
									<input type="text" autocomplete="off" class="form-control wpsc_datetime" name="custom_filter[<?php echo $field->slug?>][to]" value="<?php echo isset($filter['custom_filter'][$field->slug]['to'])?$filter['custom_filter'][$field->slug]['to']:'';?>" placeholder="<?php _e('To','supportcandy')?>">
								</div>
							</div>
							<?php
							
						}elseif($wpsc_tf_type=='21'){
							$time_format = get_term_meta($field->term_id, 'wpsc_time_format',true);
							if($time_format == '12'){
								$tr = 'hh:mm:ss tt';
							}elseif($time_format == '24'){
								$tr = 'HH:mm:ss';
							}

							if( isset($filter['custom_filter'][$field->slug]['from']) && $filter['custom_filter'][$field->slug]['from'] && isset($filter['custom_filter'][$field->slug]['to']) && $filter['custom_filter'][$field->slug]['to'] ){
								if($time_format == '12'){
									$from = date("H:i:s",strtotime($filter['custom_filter'][$field->slug]['from']));
									$to   = date("H:i:s",strtotime($filter['custom_filter'][$field->slug]['to']));
								}elseif($time_format == '24'){
									$from = $filter['custom_filter'][$field->slug]['from'];
									$to = $filter['custom_filter'][$field->slug]['to'];
								}
								$meta_query[] = array(
									'key'     => $field->slug,
									'value'   => array( 
										$from,
 									  	$to,
									),
									'compare' => 'BETWEEN',
									'type' => 'DATE',
								);
							}
							
							?>
							<div class="row form-group">
								<label style="width:100%;padding-left:15px;"><?php echo __(htmlentities($label),'supportcandy')?></label>
								<div class="col-sm-6">
									<input type="text" autocomplete="off" class="form-control <?php echo $field->slug?>" name="custom_filter[<?php echo $field->slug?>][from]" value="<?php echo isset($filter['custom_filter'][$field->slug]['from'])?$filter['custom_filter'][$field->slug]['from']:'';?>" placeholder="<?php _e('From','supportcandy')?>">
								</div>
								<div class="col-sm-6">
									<input type="text" autocomplete="off" class="form-control <?php echo $field->slug?>" name="custom_filter[<?php echo $field->slug?>][to]" value="<?php echo isset($filter['custom_filter'][$field->slug]['to'])?$filter['custom_filter'][$field->slug]['to']:'';?>" placeholder="<?php _e('To','supportcandy')?>">
								</div>
							</div>
							<script>
								 jQuery('.<?php echo $field->slug?>').timepicker({
									timeFormat : '<?php echo $tr ?>'
		 						}); 
							</script>
							

							<?php	
						}elseif($wpsc_tf_type == '6'){
							if( isset($filter['custom_filter'][$field->slug]['from']) && $filter['custom_filter'][$field->slug]['from'] && isset($filter['custom_filter'][$field->slug]['to']) && $filter['custom_filter'][$field->slug]['to'] ){
									$meta_query[] = array(
										'key'     => $field->slug,
										'value'   => array( 
											get_date_from_gmt($wpscfunction->calenderDateFormatToDateTime($filter['custom_filter'][$field->slug]['from'])),
											get_date_from_gmt($wpscfunction->calenderDateFormatToDateTime($filter['custom_filter'][$field->slug]['to'])),
										),
										'compare' => 'BETWEEN',
										'type' => 'DATE',
									);

								}
							
								?>
								<div class="row form-group">
									<label style="width:100%;padding-left:15px;"><?php echo __(htmlentities($label),'supportcandy')?></label>
									<div class="col-sm-6">
										<input type="text" autocomplete="off" class="form-control wpsc_df_from <?php echo $field->slug ?> " name="custom_filter[<?php echo $field->slug?>][from]" value="<?php echo isset($filter['custom_filter'][$field->slug]['from'])?$filter['custom_filter'][$field->slug]['from']:'';?>" placeholder="<?php _e('From','supportcandy')?>">
									</div>
									<div class="col-sm-6">
										<input type="text" autocomplete="off" class="form-control wpsc_df_to <?php echo $field->slug ?> " name="custom_filter[<?php echo $field->slug?>][to]" value="<?php echo isset($filter['custom_filter'][$field->slug]['to'])?$filter['custom_filter'][$field->slug]['to']:'';?>" placeholder="<?php _e('To','supportcandy')?>">
									</div>
								</div>
									
							<script>
							
								jQuery(".wpsc_df_from.<?php echo $field->slug?>").datepicker({
									dateFormat : '<?php echo get_option('wpsc_calender_date_format')?>',
									showAnim : 'slideDown',
									changeMonth: true,
									changeYear: true,
									yearRange: "-100:+100",
									onSelect: function (date) {
										var date2 = jQuery(".wpsc_df_from.<?php echo $field->slug?>").datepicker('getDate');
										date2.setDate(date2.getDate() + 1);
										jQuery(".wpsc_df_to.<?php echo $field->slug?>").datepicker('setDate', date2);
										//sets minDate to dt1 date + 1
										jQuery(".wpsc_df_to.<?php echo $field->slug?>").datepicker('option', 'minDate', date2);
									}
								});
								
								jQuery(".wpsc_df_to.<?php echo $field->slug?>").datepicker({
									dateFormat : '<?php echo get_option('wpsc_calender_date_format')?>',
									showAnim : 'slideDown',
									changeMonth: true,
									changeYear: true,
									yearRange: "-100:+100",
									onClose: function () {
										var dt1 = jQuery(".wpsc_df_from.<?php echo $field->slug?>").datepicker('getDate');
										var dt2 = jQuery(".wpsc_df_to.<?php echo $field->slug?>").datepicker('getDate');
										//check to prevent a user from entering a date below date of dt1
										if (dt2 <= dt1) {
											var minDate = jQuery(".wpsc_df_to.<?php echo $field->slug?>").datepicker('option', 'minDate');
											jQuery(".wpsc_df_to.<?php echo $field->slug?>").datepicker('setDate', minDate);
										}
									}
								});

						   </script>
						<?php
						}else {
							if( isset($filter['custom_filter'][$field->slug]['from']) && $filter['custom_filter'][$field->slug]['from'] && isset($filter['custom_filter'][$field->slug]['to']) && $filter['custom_filter'][$field->slug]['to'] ){
								$from_date = $wpscfunction->get_utc_date_str($wpscfunction->calenderDateFormatToDateTime($filter['custom_filter'][$field->slug]['from']));
								$to_date = $wpscfunction->get_utc_date_str($wpscfunction->calenderDateFormatToDateTime($filter['custom_filter'][$field->slug]['to']));
								$meta_query[] = array(
									'key'     => $field->slug,
									'value'   => array( 
										$from_date,
										$to_date
									),
									'compare' => 'BETWEEN',
									'type' => 'DATE',
								);
							}
						
							?>
							<div class="row form-group">
								<label style="width:100%;padding-left:15px;"><?php echo __(htmlentities($label),'supportcandy')?></label>
								<div class="col-sm-6">
									<input type="text" autocomplete="off" class="form-control wpsc_df_from <?php echo $field->slug ?>" name="custom_filter[<?php echo $field->slug?>][from]" value="<?php echo isset($filter['custom_filter'][$field->slug]['from'])?$filter['custom_filter'][$field->slug]['from']:'';?>" placeholder="<?php _e('From','supportcandy')?>">
								</div>
								<div class="col-sm-6">
									<input type="text" autocomplete="off" class="form-control wpsc_df_to <?php echo $field->slug ?>" name="custom_filter[<?php echo $field->slug?>][to]" value="<?php echo isset($filter['custom_filter'][$field->slug]['to'])?$filter['custom_filter'][$field->slug]['to']:'';?>" placeholder="<?php _e('To','supportcandy')?>">
								</div>
							</div>
							<script>
								jQuery(".wpsc_df_from.<?php echo $field->slug?>").datepicker({
									dateFormat : '<?php echo get_option('wpsc_calender_date_format')?>',
									showAnim : 'slideDown',
									changeMonth: true,
									changeYear: true,
									yearRange: "-100:+100",
									onSelect: function (date) {
										var date2 = jQuery(".wpsc_df_from.<?php echo $field->slug?>").datepicker('getDate');
										date2.setDate(date2.getDate() + 1);
										jQuery(".wpsc_df_to.<?php echo $field->slug?>").datepicker('setDate', date2);
										//sets minDate to dt1 date + 1
										jQuery(".wpsc_df_to.<?php echo $field->slug?>").datepicker('option', 'minDate', date2);
									}
								});
								
								jQuery(".wpsc_df_to.<?php echo $field->slug?>").datepicker({
									dateFormat : '<?php echo get_option('wpsc_calender_date_format')?>',
									showAnim : 'slideDown',
									changeMonth: true,
									changeYear: true,
									yearRange: "-100:+100",
									onClose: function () {
										var dt1 = jQuery(".wpsc_df_from.<?php echo $field->slug?>").datepicker('getDate');
										var dt2 = jQuery(".wpsc_df_to.<?php echo $field->slug?>").datepicker('getDate');
										//check to prevent a user from entering a date below date of dt1
										if (dt2 <= dt1) {
											var minDate = jQuery(".wpsc_df_to.<?php echo $field->slug?>").datepicker('option', 'minDate');
											jQuery(".wpsc_df_to.<?php echo $field->slug?>").datepicker('setDate', minDate);
										}
									}
								});
								</script>
							<?php
							
						}
						
					}
					?>
		  </div>
		     <script>
				    jQuery(document).ready(function(){
				
				       jQuery('.wpsc_datetime').datetimepicker({
						 dateFormat : '<?php echo get_option('wpsc_calender_date_format')?>',
						  showAnim : 'slideDown',
							changeMonth: true,
			        changeYear: true,
						 timeFormat: 'HH:mm:ss'
					 });

					jQuery( ".wpsc_search_autocomplete" ).autocomplete({
			      minLength: 0,
			      appendTo: jQuery('.wpsc_search_autocomplete').parent(),
			      source: function( request, response ) {
			        var term = request.term;
			        request = {
			          action: 'wpsc_tickets',
			          setting_action : 'filter_autocomplete',
			          term : term,
								field : jQuery(this.element).data('field'),
			        }
			        jQuery.getJSON( wpsc_admin.ajax_url, request, function( data, status, xhr ) {
			          response(data);
			        });
			      },
						select: function (event, ui) {
			        var html_str = '<li class="wpsp_filter_display_element">'
															+'<div class="flex-container">'
																+'<div class="wpsp_filter_display_text">'
																	+ui.item.label
																	+'<input type="hidden" name="custom_filter['+ui.item.slug+'][]" value="'+ui.item.flag_val+'">'
																+'</div>'
																+'<div class="wpsp_filter_display_remove" onclick="wpsc_remove_filter(this);"><i class="fa fa-times"></i></div>'
															+'</div>'
														+'</li>';
							jQuery('#tf_'+ui.item.slug+' .wpsp_filter_display_container').append(html_str);
							jQuery(this).val(''); return false;
			      }
			    }).focus(function() {
							jQuery(this).autocomplete("search", "");
					});
				});
				function wpsc_set_save_ticket_filter(){  
				  var filter_name = jQuery('#wpsc_filter_label').val().trim();
				  if (filter_name.length == 0) {
				    jQuery('#wpsc_filter_label').val('').focus();
				    return;
				  }
				  var dataform = new FormData(jQuery('#frm_additional_filters')[0]);
				  console.log("get_ticket_list.php 453", dataform);
				  dataform.append('filter_name', filter_name);
				  dataform.append('action', 'wpsc_tickets');
				  dataform.append('setting_action', 'set_save_ticket_filter');
				  jQuery('.wpsc_popup_action').text('<?php _e('Please wait ...','supportcandy')?>');
				  jQuery('.wpsc_popup_action, #wpsc_popup_body input').attr("disabled", "disabled");
				  jQuery.ajax({
				    url: wpsc_admin.ajax_url,
				    type: 'POST',
				    data: dataform,
				    processData: false,
				    contentType: false
				  })
				  .done(function (response_str) {    
				    wpsc_modal_close();      
				    wpsc_get_ticket_list();    
				  });      
				}
			</script>
			
			<div class="row wpsp_filter_footer" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_footer_bg_color']?> !important;">
				<div class="col-sm-12">
					<button type="submit" id="wpsc_load_apply_filter_btn" class="btn wpsp_filter_btn" style="background-color:<?php echo $wpsc_appearance_ticket_list['wpsc_apply_filter_btn_bg_color']?> !important;color:<?php echo $wpsc_appearance_ticket_list['wpsc_apply_filter_btn_text_color']?> !important;border-color:<?php echo $wpsc_appearance_ticket_list['wpsc_apply_filter_btn_border_color']?> !important;"><?php _e('Apply Filter','supportcandy')?></button>
					<?php /* HIDE SAVE FILTER INSIDE FILTER DROPDOWN
					<button type="button" id="wpsc_save_ticket_filter_btn" onclick="wpsc_get_save_ticket_filter();" class="btn wpsp_filter_btn" style="background-color:<?php echo $wpsc_appearance_ticket_list['wpsc_save_filter_btn_bg_color']?> !important;color:<?php echo $wpsc_appearance_ticket_list['wpsc_save_filter_btn_text_color']?> !important;border-color:<?php echo $wpsc_appearance_ticket_list['wpsc_save_filter_btn_border_color']?> !important;"><?php _e('Save Filter','supportcandy')?></button>
					*/?>
					<button type="button" id="wpsc_load_close_filter" onclick="wpsc_close_custom_filter();" class="btn wpsp_filter_btn" style="background-color:<?php echo $wpsc_appearance_ticket_list['wpsc_close_filter_btn_bg_color']?> !important;color:<?php echo $wpsc_appearance_ticket_list['wpsc_close_filter_btn_text_color']?> !important;border-color:<?php echo $wpsc_appearance_ticket_list['wpsc_close_filter_btn_border_color']?> !important;"><?php _e('Close','supportcandy')?></button>


				</div>
			</div>
		</div>
		<input type="hidden" name="filter" value="all">
	</div>
	
	<input type="hidden" name="action" value="wpsc_tickets">
	<input type="hidden" name="setting_action" value="set_custom_filter">
	<input type="hidden" id="wpsc_pg_no" name="page_no" value="<?php echo htmlentities($filter['page'])?>">
	<input type="hidden" id="wpsc_th_orderby" name="orderby" value="<?php echo htmlentities($filter['orderby'])?>">
	<input type="hidden" id="wpsc_th_order" name="order" value="<?php echo htmlentities($filter['order'])?>">
	
</form>

<?php


$search = '';
$is_search = trim($filter['custom_filter']['s']) ? true : false;
if($is_search){
	$search = trim($filter['custom_filter']['s']);
}

$active = 1;
if ($filter['label'] == 'deleted') {
	$active = 0;
}

$meta_query[] = array(
	'key'     => 'active',
	'value'   => $active,
	'compare' => '='
);

	/*Added By Gulam*/


	if( $_SESSION['user_type'] == 'subscriber'){


		$meta_query[] = array(
		'relation' => 'AND',
		array(
			'key'            => 'ticket_status',
			'value'          => 3,
			'compare'        => '!='
			),
		);
		
	}

	

	


/*Added By Gulam*/

$orderby      = sanitize_text_field($filter['orderby']=='ticket_id'?'id':$filter['orderby']);
$order        = sanitize_text_field($filter['order']);
$current_page = sanitize_text_field($filter['page']);
$select_str   = 'SQL_CALC_FOUND_ROWS DISTINCT t.*';

$sql          = $wpscfunction->get_sql_query( $select_str, $meta_query, $search, $orderby, $order, $post_per_page, $current_page );
$sql1          = $wpscfunction->get_sql_query( $select_str, $meta_query, $search, $orderby, $order); 

/*echo "<pre>";
print_r($filter);
echo "<pre>";*/



$tickets      = $wpdb->get_results($sql);
/*echo "<pre>";
print_r($meta_query);
echo "<pre>";
*/

$tickets1      = $wpdb->get_results($sql1);
$total_items  = $wpdb->get_var("SELECT FOUND_ROWS()");
$ticket_list  = json_decode(json_encode($tickets), true);
$ticket_list1  = json_decode(json_encode($tickets1), true);
$total_pages  = ceil($total_items/$post_per_page);

if( $total_items<=$current_page*$post_per_page){
 $no_of_tickets = $total_items;
}
else {
 $no_of_tickets = $current_page*$post_per_page;
}



if ($current_user->has_cap('wpsc_agent') && $_SESSION['user_type'] == 'supplier') {

	$ticket_list_items = get_terms([
		'taxonomy'   => 'wpsc_ticket_custom_fields',
		'hide_empty' => false,
		'orderby'    => 'meta_value_num',
		'meta_key'	 => 'wpsc_tl_agent_load_order',
		'order'    	 => 'ASC',
		'meta_query' => array(
			'relation' => 'AND',
	    array(
	      'key'       => 'wpsc_allow_ticket_list',
	      'value'     => '1',
	      'compare'   => '='
	    ),
	    array(
	      'key'       => 'wpsc_agent_ticket_list_status',
	      'value'     => '1',
	      'compare'   => '='
	    )
		),
	]);
} else {
	$ticket_list_items = get_terms([
		'taxonomy'   => 'wpsc_ticket_custom_fields',
		'hide_empty' => false,
		'orderby'    => 'meta_value_num',
		'meta_key'	 => 'wpsc_tl_customer_load_order',
		'order'    	 => 'ASC',
		'meta_query' => array(
			'relation' => 'AND',
	    array(
	      'key'       => 'wpsc_allow_ticket_list',
	      'value'     => '1',
	      'compare'   => '='
	    ),
	    array(
	      'key'       => 'wpsc_customer_ticket_list_status',
	      'value'     => '1',
	      'compare'   => '='
	    )
		),
	]);
}
?>
<script>
var link = true;
</script>


<?php 

/*echo '<pre>';
print_r($ticket_list_items); 
echo '</pre>';*/
include_once WPSC_ABSPATH . 'includes/admin/tickets/ticket_list/class-ticket-list-field-format.php';
	$format = new WPSC_Ticket_List_Field();


$ret = $conterArr = [];
	if($ticket_list1){
		foreach($ticket_list1 as $ticket1){

			foreach ($ticket_list_items as $list_item) {			
				
				$ret = $format->get_print_ticket_status_count($list_item,$ticket1, $conterArr);
				$conterArr = $ret;

			}

		}
		//echo "<pre>"; print_r($ret); echo "</pre>";
	}

 




?>


<div class="col-sm-6 col-sm-offset-6 col-xs-12 wpsc_ticket_count" style="margin-top:-20px !important;text-align:right;font-size:12px;padding-right:0; display: none;">
		<?php
		if(apply_filters('wpsc_show_ticket_count',true)){
			if( $total_items > $post_per_page ){?>
				 <strong><?php echo ($current_page*$post_per_page-$post_per_page)+1;?></strong>-<strong><?php echo htmlentities($no_of_tickets);?></strong> <?php _e('of' ,'supportcandy'); ?> <strong><?php echo htmlentities($total_items);?></strong> <?php ($total_items >1)?_e('Orders','supportcandy'):_e('Order','supportcandy');?>
				<?php	
			} else {
		     ?>
				   <strong><?php echo htmlentities($total_items);?> <?php ($total_items >1 ) ? _e('Orders','supportcandy'): _e('Order','supportcandy');?> </strong>
			<?php 
			 }
		}
	 ?>
</div>

<?php 
/*echo '<pre>';
print_r($ticket_list); 
echo '</pre>';
echo $filter['custom_filter']['ticket_status'][0];*/





/*if(!empty($ticket_list) || !empty($filter['custom_filter']['ticket_status'][0])){*/

	





 ?>

<div class="table-responsive">
	<div class="col-xs-12 p-0">
		<ul class="btn-ul">

			<?php 

					$current_user = wp_get_current_user();
                    $role = get_userdata($current_user->ID); 

          if($_SESSION['user_type'] != 'subscriber'){


          	
			?>

			<li class="btn-li w-100"><button onclick="wpsc_set_button_filter('3');" class="btn btn-sm-lg btn-primary w-100" id="sentfor">OPEN (TO SEND) <?php echo $ret['Open(To send)']; ?></button></li>
			<li class="btn-li w-100"><button onclick="wpsc_set_button_filter('4');" class="btn btn-sm-lg btn-primary w-100" id="sentreview">Sent for Approval <?php echo $ret['Sent for Approval']; ?></button></li>
<?php }else{ ?>
<li class="btn-li w-100"><button onclick="wpsc_set_button_filter('4');" class="btn btn-sm-lg btn-primary w-100" id="sentreview">WAITING FOR YOUR APPROVAL <?php echo $ret['WAITING FOR YOUR APPROVAL']; ?></button></li>

<?php } ?>
			
		
			<li class="btn-li w-100"><button onclick="wpsc_set_button_filter('6');" class="btn btn-sm-lg btn-primary w-100" id="approved">Approved <?php echo $ret['Approve']; ?></button></li>
			<li class="btn-li w-100"><button onclick="wpsc_set_button_filter('74');" class="btn btn-sm-lg btn-primary w-100" id="rejected">Rejected <?php echo $ret['Reject']; ?></button></li>
			<li class="btn-li w-100"><button onclick="wpsc_set_button_filter('75');" class="btn btn-sm-lg btn-primary w-100" id="modification">Modification Requested <?php echo $ret['Request Modification']; ?></button></li>
		</ul>
	</div>
<table id="tbl_wpsc_ticket_list" class="table" >
  <tr>
  	<!-- style="background-color:<?php echo $wpsc_appearance_ticket_list['wpsc_ticket_list_header_bg_color']?> !important;color:<?php echo $wpsc_appearance_ticket_list['wpsc_ticket_list_header_text_color']?> !important;" -->
    <th class="wpsc_th_check_all" ><input id="chk_all_ticket_list" onchange="toggle_list_checkboxes(this);" type="checkbox" /></th>
		<?php
		$label_rank = 0;
		foreach ($ticket_list_items as $list_item) {

			

			$label_rank++;

			$label         = get_term_meta( $list_item->term_id, 'wpsc_tf_label', true);
			$allow_orderby = get_term_meta( $list_item->term_id, 'wpsc_allow_orderby', true);

			if($_SESSION['user_type'] == 'supplier'){

				$labelrank = 4;

			}elseif($_SESSION['user_type'] == 'subscriber'){
				$labelrank = 5;
			}				
			?>

			<?php if($label_rank == $labelrank) : 


				?>
				<th class="wpsc_th_check_all" >Description</th>
			<?php endif; ?>
			<th class="wpsc_th_<?php echo $list_item->slug ?>"onclick="<?php echo $allow_orderby ? 'wpsc_header_sort(\''.$list_item->slug.'\')' : ''?>" >
				<?php 
				_e($label,'supportcandy');
				if( $filter['orderby']==$list_item->slug && $filter['order'] =='ASC' ){
						?> <i class="fa fa-caret-down"></i><?php
				} else if( $filter['orderby']==$list_item->slug && $filter['order'] =='DESC' ){
						?> <i class="fa fa-caret-up"></i><?php
				}
				?>
			</th>
			<?php
		}


		?>
		<?php do_action('wpsc_print_ticket_list_table_header'); ?>
  </tr>
	
	<?php
	
	include_once WPSC_ABSPATH . 'includes/admin/tickets/ticket_list/class-ticket-list-field-format.php';
	$format = new WPSC_Ticket_List_Field();
	
		if($ticket_list){
			foreach($ticket_list as $ticket){
				?>
				<tr class="wpsc_tl_row_item" data-id="<?php echo $ticket['id']?>" onclick="if(link)wpsc_get_individual_ticket(this);">
					<td onmouseover="link=false;" onmouseout="link=true;">
						<input type="checkbox" name="chk_ticket_list_item[]" class="chk_ticket_list_item" onchange="toggle_ticket_list_actions();" value="<?php echo $ticket['id']; ?>"/>
					</td>
					<?php
					$label_rank = 0;
					// echo "<pre>"; print_r($ticket_list_items);
					foreach ($ticket_list_items as $list_item) {
                      /*echo "<pre>";
						print_r($list_item);*/
						$label_rank++;

						
						if($label_rank == $labelrank) { ?>
							<!-- Added By Gulam -->
							<?php
							$post = get_post($ticket['historyId']);
							$args = array(
									'post_type'      => 'wpsc_ticket_thread',
									'post_status'    => 'publish',
									'orderby'        => 'ID',
									'order'          => 'ASC',
									'posts_per_page' => -1,
									'meta_query'     => array(
										'relation' => 'AND',
										array(
										  'key'     => 'ticket_id',
									      'value'   => $ticket['id'],
									      'compare' => '='
										))
									);
								$threads = get_posts($args);



							?>
							<td>
							<?php 
								$output =  substr($threads[0]->post_content, 0, 100);
								if($output != 'NA')
								{echo strip_tags($output);}
							?>
							</td> 

							<!-- End Added By Gulam -->
						<?php } ?>
						<td style="<?php echo $list_item->slug == 'ticket_subject' || $list_item->slug == 'assigned_agent' ? 'white-space: normal;' : ''?>"><?php echo $format->print_field($list_item,$ticket); ?>
						
						</td>
						<?php
					}

					do_action('wpsc_print_fields_in_ticket_list',$ticket); ?>
				</tr>
				<?php
			}
			$current_page = sanitize_text_field($filter['page']);
			}else{
				?>
				<tr>
					<?php 
					if($_SESSION['user_type'] != 'subscriber'){ ?>
					<td colspan="8"><?php _e('No orders found!','supportcandy')?></td>
					<?php }else{ ?>
                     <td colspan="8"><?php _e('No orders found!','supportcandy')?></td>
					<?php } ?>
				</tr>
				<?php
			}
			?>
		</table>
	</div>


	
		
		
			<!-- Role -->
			       <?php 
					if($_SESSION['user_type'] == 'supplier'){ ?>
						
					<button type="button" id="wpsc_load_list_new_ticket_btn" onclick="wpsc_get_create_ticket();" class="btn btn-sm wpsc_create_ticket_btn" ><i class="fa fa-plus"></i> <?php _e('New Order','supportcandy')?></button>
					<?php } ?>
			
		




		<?php 
		$wpsc_tl_row_item_css = 'background-color:'.$wpsc_appearance_ticket_list['wpsc_ticket_list_item_mo_bg_color'].' !important;color:'.$wpsc_appearance_ticket_list['wpsc_ticket_list_item_mo_text_color'].' !important;';
		?>
		<style type="text/css">
			.wpsc_tl_row_item:hover{
				<?php echo htmlentities($wpsc_tl_row_item_css)?>
			}
		</style>
		<?php
		if($ticket_list) : ?>	
		<div class="row wpsc_pagination_prev_next_btn" style="margin-bottom:20px;">
	  	  <div class="col-md-4 col-md-offset-4 wpsc_ticket_list_nxt_pre_page" style="text-align: center;">
		   <?php 
		   if($total_pages != 1){ ?>
           		<button class="btn btn-default btn-sm" <?php echo $filter['page']==1? 'disabled' : ''?> onclick="wpsc_ticket_first_page();"><i class="fas fa-fast-backward" aria-hidden="true"></i></button>  
		   		<button class="btn btn-default btn-sm" <?php echo $filter['page']==1? 'disabled' : ''?> onclick="wpsc_ticket_prev_page();"><i class="fas fa-step-backward" aria-hidden="true"></i></button>
		   		<strong><?php echo $current_page ?></strong> <?php _e('of','supportcandy')?> <strong><?php echo $total_pages?></strong> <?php _e('Pages','supportcandy') ?>
		   		<button class="btn btn-default btn-sm" <?php echo $filter['page']==$total_pages? 'disabled' : ''?> onclick="wpsc_ticket_next_page();"><i class="fas fa-step-forward" aria-hidden="true"></i></button>
		   		<button class="btn btn-default btn-sm" onclick="wpsc_last_ticket_page();" <?php echo $filter['page']==$total_pages? 'disabled' : ''?>><i class="fas fa-fast-forward" aria-hidden="true"></i></button>
		   	<?php 
			} ?>
		  </div>
		</div>
		<?php endif; ?>

<script>
	jQuery(document).ready(function() {
		jQuery('#wpsc_ticket_search').on("keypress", function(e) {
			if (e.keyCode == 13) {
				jQuery('#wpsc_pg_no').val('1');
			}
		});
		
		jQuery('#wpsc_load_apply_filter_btn').on("click", function(e) {
			jQuery('#wpsc_pg_no').val('1');
		});
	});
	<?php do_action('wpsc_print_ext_js_ticket_list');	?>
 
	function wpsc_last_ticket_page(){
		var page_no = parseInt(jQuery('#wpsc_pg_no').val().trim());
		var last_page=<?php echo json_encode($total_pages); ?>;
		if(page_no != last_page){
			page_no = last_page;
			jQuery('#wpsc_pg_no').val(page_no);
			wpsc_set_custom_filter();
		}  
	}

	function wpsc_ticket_first_page(){
		var page_no = parseInt(jQuery('#wpsc_pg_no').val().trim());
		if(page_no > 1){ 
			page_no = 1;
			jQuery('#wpsc_pg_no').val(page_no);
			wpsc_set_custom_filter();
		}
	}

</script>




 