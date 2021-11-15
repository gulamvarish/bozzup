<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpscfunction,$current_user;
$wpsc_close_ticket_status = get_option('wpsc_close_ticket_status');
$status_id = $wpscfunction->get_ticket_fields($ticket_id,'ticket_status');

//$ticket_data 	= $wpscfunction->get_ticket($ticket_id);
//$status_id   	= $ticket_data['ticket_status'];

$fields = get_terms([
	'taxonomy'   => 'wpsc_ticket_custom_fields',
	'hide_empty' => false,
 ]);
 
foreach ($fields as $field) {
	if($field->name=='ticket_description'){
		$term_id=$field->term_id;
	}
}

$wpsc_appearance_individual_ticket_page = get_option('wpsc_individual_ticket_page');
$reply_to_close_ticket = get_option('wpsc_allow_reply_to_close_ticket');
$wpsc_close_ticket_group = get_option('wpsc_close_ticket_group');
$wpsc_allow_reply_to_public_tickets = get_option('wpsc_allow_reply_to_public_tickets');


$allow_reply = true;
if(($current_user->has_cap('wpsc_agent') && !$current_user->has_cap('manage_options') && in_array($status_id, $wpsc_close_ticket_group) && !in_array('agents', $reply_to_close_ticket) )){
	$allow_reply = false;
}elseif((!$current_user->has_cap('wpsc_agent') && in_array($status_id, $wpsc_close_ticket_group) && !in_array('customer', $reply_to_close_ticket) )){
	$allow_reply = false;
}elseif(!$wpsc_allow_reply_to_public_tickets && ($current_user->user_email != $customer_email) && !($current_user->has_cap('wpsc_agent')) && !($current_user->has_cap('manage_options'))){
		$allow_reply = false;
}

$wpsc_allow_attach_reply_form = get_option('wpsc_allow_attach_reply_form');
$wpsc_attachment_max_filesize = get_option('wpsc_attachment_max_filesize');


/*Added By Gulam*/

$status_id;

if ($status_id == 75 && $_SESSION['user_type']=='subscriber') {

	$allow_reply = false;

}elseif($status_id==6 || $status_id == 74){
$allow_reply = false;
}else{

	$allow_reply = apply_filters('wpsc_show_ticket_reply_editor',$allow_reply,$ticket_id);
}
 



/*End Added By Gulam*/



if(($allow_reply || $_SESSION['user_type'] == 'supplier') && $_SESSION['expire'] !='account-expire'){


?>
	<div class="row wpsc_reply_widget">
	  <form id="wpsc_frm_tkt_reply" action="index.html" method="post">
	    <textarea id="wpsc_reply_box" name="reply_body" class="wpsc_textarea"></textarea>
		<?php
		$wpsc_reply_bcc = get_option('wpsc_reply_bcc_visibility');
		if($wpsc_reply_bcc && apply_filters('wpsc_show_hide_bcc_field',true)){?>
        	<input class="form-control" style="margin-top:10px;" type="text" name="reply_bcc" id="reply_bcc" placeholder="<?php _e('BCC (Comma separated list)','supportcandy');?>" />
		<?php 
		} ?>
		<div class="col-sm-4 attachment">
	      <div class="row attachment_link" role="<?php echo $current_user->roles[0]; ?>">
					<?php 
					$notice_flag = false;
					if( (in_array('customers',$wpsc_allow_attach_reply_form) && is_user_logged_in() && !$current_user->has_cap('wpsc_agent')) ||
							(in_array('agents',$wpsc_allow_attach_reply_form) && $current_user->has_cap('wpsc_agent')) || $current_user->has_cap('manage_options') ){
								$notice_flag = true;
						?>

						<span  onclick="wpsc_attachment_upload('<?php echo 'attach_'.$term_id?>','desc_attachment');"><i><img src="<?php echo plugin_dir_url('/').'supportcandy/asset/images/icon/attachment.png'; ?>"></i><?php _e('Attach file','supportcandy')?></span>
						<?php
					} ?>
	        <?php
	        /*
	         if ($wpscfunction->has_permission('add_note',$ticket_id)):?>
	          <span id="wpsc_insert_macros" onclick="wpsc_get_templates()" ><?php _e('Insert Macros','supportcandy')?></span>
	        <?php endif;
			*/
	        ?>

			<?php do_action('wpsc_add_addon_tab_after_macro');?>
			<div class="form-group">
			   <?php					
				$wpsc_allow_attachment_type = get_option('wpsc_allow_attachment_type');
				$max_attachment_limit = get_option('wpsc_attachment_max_filesize');
				$wpsc_show_attachment_notice  = get_option('wpsc_show_attachment_notice');
				    if($wpsc_show_attachment_notice && $notice_flag){
						$attach_notice = get_option('wpsc_attachment_notice');
						?> 
                       <p class="help-block"> <i> <?php echo $attach_notice;?></i></p>
			  <?php }?>
		    </div>

	      </div>
	      <div id="<?php echo 'attach_'.$term_id?>" class="row attachment_container"></div>
		  
		  <?php do_action('wpsc_rf_attachment_container',$ticket_id); ?>

		</div>
	    <div class="col-sm-8 submit">
				
				<?php if ($wpscfunction->has_permission('reply_ticket',$ticket_id)): /*?>
								<button type="button" id="wpsc_individual_submit_reply_btn" onclick="javascript:wpsc_submit_reply('reply');" class="btn" style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_submit_reply_btn_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_submit_reply_btn_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_submit_reply_btn_border_color']?> !important;">
									<i class="fa fa-reply"></i> <?php _e('Submit Reply','supportcandy')?> 
								</button>
	      		<?php */ endif; ?>

	      		<!-- ADDED CUSTOM STATUS -->
	      		<?php
		      		$ticket_data 	= $wpscfunction->get_ticket($ticket_id);
					$status_id   	= $ticket_data['ticket_status'];
					$wpsc_custom_status_localize   = get_option('wpsc_custom_status_localize');
				?>
	      		<div class="form-group col-12">
					<label for="wpsc_default_ticket_status"><?php //_e('Order Status','supportcandy');?></label>

					<!-- <input tyep="hidden" name="status" id="cstm_stats" value=""> -->
						<?php
						$statuses = get_terms([
							'taxonomy'   => 'wpsc_statuses',
							'hide_empty' => false,
							'orderby'    => 'meta_value_num',
							'order'    	 => 'ASC',
							'meta_query' => array('order_clause' => array('key' => 'wpsc_status_load_order')),
						]);
						
						global $current_user;

                       $btn_array = array();

						/* Added By Gulam */
			      		foreach ( $statuses as $status ) :
			      		    
			      		       $user_roles = $current_user->roles;
			      		        $user_roles[0];

                                if ($_SESSION['user_type'] == 'subscriber') {
                                     
                                    if($wpsc_custom_status_localize['custom_status_'.$status->term_id] != 'Open(To send)' && $wpsc_custom_status_localize['custom_status_'.$status->term_id] != 'Sent for Approval'){       

							         $btn_array[] = '<button onclick="javascript:wpsc_before_submit_reply('.$status->term_id.');" type="button" class="btn text-white" id="btnid'.$status->term_id.'" statusid="'.$status->term_id.'">'.$wpsc_custom_status_localize['custom_status_'.$status->term_id].'</button>';
                                    }
                            
                                }else {
                                   
							       /*For Admin Area*/
							     // echo  $wpsc_custom_status_localize['custom_status_'.$status->term_id];

							    if($wpsc_custom_status_localize['custom_status_'.$status->term_id] == 'Open(To send)' || $wpsc_custom_status_localize['custom_status_'.$status->term_id] == 'Sent for Approval'){

							    	// change status label
							    	if($wpsc_custom_status_localize['custom_status_'.$status->term_id] == 'Open(To send)') {
							    		$wpsc_custom_status_localize['custom_status_'.$status->term_id] = 'SAVE AND SEND LATER';
							    		$btnColor = "btn btn-info";
							    	} else if($wpsc_custom_status_localize['custom_status_'.$status->term_id] == 'Sent for Approval') {
							    		$wpsc_custom_status_localize['custom_status_'.$status->term_id] = 'SEND FOR APPROVAL';
							    		$btnColor = "btn btn-success";
							    	}

							      $btn_array[] = '<button type="button"
							       onclick="wpsc_before_submit_reply('.$status->term_id.')" class="'.$btnColor.'" statusid="'.$status->term_id.'">'.$wpsc_custom_status_localize['custom_status_'.$status->term_id].'</button>';
							      
							   }
							    /*For Admin Area*/
                             }

                                
							
						endforeach;
						/* Added By Gulam */
						?>

					<input type="hidden" name="status" value="" class="cstm_statusvalue" id="cstm_statusvalue">

					<?php
					[$btn_array[0], $btn_array[1]] = [$btn_array[1], $btn_array[0]];
					foreach ($btn_array as $key => $value) {
						echo $value;
					}


					?>
				</div>

				<?php 
				/*
				if ($wpscfunction->has_permission('add_note',$ticket_id)):?>
		            <button type="button" id="wpsc_individual_add_note_btn" onclick="javascript:wpsc_submit_reply('note');" class="btn"style="background-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_other_reply_form_btn_bg_color']?> !important;color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_other_reply_form_btn_text_color']?> !important;border-color:<?php echo $wpsc_appearance_individual_ticket_page['wpsc_other_reply_form_btn_border_color']?> !important;">
				          <i class="far fa-comment"></i> <?php _e('Add Note','supportcandy')?> 
				        </button>
	      		<?php endif; */?>
				<?php do_action('wpsc_add_addon_reply_tab');?>
	    </div>
			<input type="file" id="attachment_upload" class="hidden" onchange="">
	    <input type="hidden" name="ticket_id" value="<?php echo htmlentities($ticket_id)?>">
	    <input type="hidden" id="wpsc_nonce" name="nonce" value="<?php echo wp_create_nonce($ticket_id);?>">
			<input type="hidden" name="action" value="wpsc_tickets">
	  </form>
	</div>
<?php } ?>

<script type="text/javascript">

       
	
 
function wpsc_attachment_upload(id,name){
	jQuery('#attachment_upload').unbind('change');
	jQuery('#attachment_upload').on('change', function(){
		var flag = false;
		var file = this.files[0];
		jQuery('#attachment_upload').val('');
		var allowedExtension = ['exe','php'];
		var file_name_split = file.name.split('.');
		var file_extension = file_name_split[file_name_split.length-1];
		file_extension     = file_extension.toLowerCase(); 
		<?php 
		$attachment      = get_option('wpsc_allow_attachment_type');
		$attachment_data = explode(',' , $attachment );
		$attachment_data = array_map('trim', $attachment_data);
		$attachment_data = array_map('strtolower', $attachment_data);
		?>;
		var allowedExtensionSetting = [<?php echo '"'.implode('","', $attachment_data).'"' ?>];
		
		if(!flag && (jQuery.inArray(file_extension,allowedExtensionSetting)  <= -1 || jQuery.inArray(file_extension,allowedExtension) > -1)) {
			flag = true;
			alert("<?php _e('Attached file type not allowed!','supportcandy')?>");
		}
		
		var current_filesize=file.size/1000000;
		if(current_filesize><?php echo $wpsc_attachment_max_filesize?>){
			flag = true;
			alert('<?php echo sprintf( __('File size exceeds allowed limit (%1$s MB)!','supportcandy'), $wpsc_attachment_max_filesize );?>');
		}
		
		if(!flag){
				 
			var html_str = '<div class="row wpsc_attachment">'+
				'<div class="progress" style="float: none !important; width: unset !important;">'+
				'<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">'+
					file.name+
					'</div>'+
					'</div>'+
					'<img onclick="attachment_cancel(this);" class="attachment_cancel" src="<?php echo WPSC_PLUGIN_URL.'asset/images/close.png'?>" style="display:none;" />'+
					'</div>';
							
					jQuery('#'+id).append(html_str);
					
					var attachment = jQuery('#'+id).find('.wpsc_attachment').last();
					
					var data = new FormData();
					data.append('file',file);
					data.append('arr_name',name);
					data.append('action','wpsc_tickets');
					data.append('setting_action','upload_file');
		
				  jQuery.ajax({
						 type : 'post',
						 url : wpsc_admin.ajax_url,
						 data : data,
						 xhr : function(){
					       var xhr = new window.XMLHttpRequest();
		              xhr.upload.addEventListener("progress", function(evt){
		                  if (evt.lengthComputable) {
		                      var percentComplete = Math.floor((evt.loaded / evt.total) * 100);
		                      jQuery(attachment).find('.progress-bar').css('width',percentComplete+'%');
		                  }
		              }, false);
		              return xhr;
						 },
						 processData: false,
		         contentType: false,
						 success : function(response){
	             
							 var return_obj =JSON.parse(response);
							 
							 jQuery(attachment).find('.attachment_cancel').show();
							 
							 if(parseInt(return_obj.id) != 0){

							 	/*if(jQuery('.attachment_link').attr('role') == 'supplier'){
							 		jQuery('.attachment_link').remove();

							 	}*/
							 	jQuery('.attachment_link').hide();

								 jQuery(attachment).append('<input type="hidden" name="'+name+'[]" value="'+return_obj.id+'">');
								 jQuery(attachment).find('.progress-bar').addClass('progress-bar-success');
							 }else{
								 jQuery(attachment).find('.progress-bar').addClass('.progress-bar-danger');
							 }
						 }
						 
			    });
					
		    }
	  });
		jQuery('#attachment_upload').trigger('click');
 }

</script>



