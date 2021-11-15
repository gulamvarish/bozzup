<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb, $wpscfunction;
if (!($current_user->ID)) {exit;}

// condition to check source of calling this functionality
// It might be rest api or GUI ajax call

if (!isset($source)) {
	$term       = isset($_REQUEST) && isset($_REQUEST['term']) ? sanitize_text_field($_REQUEST['term']) : '';
	$field_slug = isset($_REQUEST) && isset($_REQUEST['field']) ? sanitize_text_field($_REQUEST['field']) : '';
}



if($field_slug == 'ticket_id'){
	$field_slug = 'id';
}

$filter = $wpscfunction->get_current_filter();
$active = 1;
if ($filter['label'] == 'deleted') {
	$active = 0;
}

/*Added By Gulam*/

/*get agaent id*/



          if($_SESSION['user_type'] == 'subscriber'){

						   $agentid   = "SELECT DISTINCT `agent_created`  FROM ".$wpdb->prefix."wpsc_ticket WHERE `customer_email` = '".$current_user->user_email."' AND `active` = '1'";

						}elseif($_SESSION['user_type'] == 'supplier'){

							 $current_agent_id  = $wpscfunction->get_current_user_agent_id();							

							  $agentid   = "SELECT *  FROM ".$wpdb->prefix."wpsc_ticket WHERE `agent_created` = '".$current_agent_id."' AND `active` = '1'";
						}





$agentids     = $wpdb->get_results($agentid);


     $agentuserid = array();

				if (isset($agentids) && !empty($agentids)) {
					
					 foreach ($agentids as $key => $value) {

					 

					 	  $termuserid = $wpdb->get_results( "SELECT * FROM $wpdb->termmeta WHERE `term_id` = '".$value->agent_created."' AND `meta_key` = 'user_id'");	

							 $user_id = $termuserid[0]->meta_value;
							 array_push($agentuserid,$user_id);

					 }
				}

			

/*Added By Gulam*/

$output = array();

switch ($field_slug) {
	
	case 'ticket_status':
	
			$statuses = get_terms([
				'taxonomy'   => 'wpsc_statuses',
				'hide_empty' => false,
				'search'     => $term,
			]);
			foreach($statuses as $status){
				// Status label updated by rkumar as per client request.
				if($status->name == 'Open(To send)' && $_SESSION['user_type'] == 'subscriber') {
					continue;
				}

				if($status->name == 'Approve') {
					$status->name = 'Approved';
				}
				if($status->name == 'Reject') {
					$status->name = 'Rejected';
				}
				if($status->name == 'Request Modification') {
					$status->name = 'Modification Requested';
				}

				if($status->name == 'Sent for Approval' && $_SESSION['user_type'] == 'subscriber') {
					$status->name = 'Waiting for your approval';
				}

				$output[] = array(
					'label'    => html_entity_decode($status->name),
					'value'    => '',
					'flag_val' => $status->term_id,
					'slug'     => $field_slug,
				);
			}
			break;
			
	case 'ticket_category':
	
			$categories = get_terms([
				'taxonomy'   => 'wpsc_categories',
				'hide_empty' => false,
				'search'     => $term,
			]);
			foreach($categories as $category){
				$output[] = array(
					'label'    => html_entity_decode($category->name),
					'value'    => '',
					'flag_val' => $category->term_id,
					'slug'     => $field_slug,
				);
			}
			break;
			
	case 'ticket_priority':
	
			$priorities = get_terms([
				'taxonomy'   => 'wpsc_priorities',
				'hide_empty' => false,
				'search'     => $term,
			]);
			foreach($priorities as $priority){
				$output[] = array(
					'label'    => html_entity_decode($priority->name),
					'value'    => '',
					'flag_val' => $priority->term_id,
					'slug'     => $field_slug,
				);
			}
			break;
			
	case 'assigned_agent':
	case 'agent_created':
			$meta = array();
			$meta['relation'] = 'OR';
			$term1 = explode(' ',$term);


			
			foreach ($term1 as $key => $value) {


				$meta[] = array(
					'key'       => 'label',
					'value'     => $value,
					'compare'   => 'LIKE' 
				);
				
				$meta[] = array(
					'key'       => 'first_name',
					'value'     => $value,
					'compare'   => 'LIKE'
				);
						
				$meta[]			 = 	array(
					'key'       => 'last_name',
					'value'     => $value,
					'compare'   => 'LIKE'
				);
				
				$meta[] = 		array(
					'key'       => 'nicename',
					'value'     => $value,
					'compare'   => 'LIKE'
				);
				
				$meta[] = 	array(
					'key'       => 'email',
					'value'     => $value,
					'compare'   => 'LIKE'
				);
			}


			$agents = get_terms([
				'taxonomy'   => 'wpsc_agents',
				'hide_empty' => false,
				'number'		=>	'',
				'orderby'    => 'label',
				'order'      => 'ASC',
				'meta_query'     => $meta
			]);
/*echo "<pre>";
			print_r($agents);*/


			$useriduniq = array();
			$agenttermid = array();
			/*echo "<pre>";
				print_r($agentuserid);*/
			
			foreach($agents as $agent){
				

				$termuserid = $wpdb->get_results( "SELECT * FROM $wpdb->termmeta WHERE `term_id` = '".$agent->term_id."' AND `meta_key` = 'user_id'");
				
				
				//print_r($termuserid);

				if(!in_array($termuserid[0]->meta_value, $useriduniq, true)){

             array_push($useriduniq, $termuserid[0]->meta_value);
             $agenttermid[$termuserid[0]->meta_value] =$agent->term_id;
        }
				
				 
				
			}
				//print_r($agenttermid);
			foreach ($useriduniq as $key => $userid) {



				if(in_array($userid, $agentuserid))			 			
  				{  


			 	  $company  = get_user_meta( $userid, 'company', true );
				
				

				$output[]   = array(
					'label'    => $company,
					'value'    => '',
					'flag_val' => $agenttermid[$userid],
					'slug'     => $field_slug,
				);
			 }
				 
			}



			/*if(!$agents){
				$output[]   = array(
					'label'    => __('None','supportcandy'),
					'value'    => '',
					'flag_val' => '0',
					'slug'     => $field_slug,
				);
			}*/
			break;
			
	case 'user_type':
		$output[]   = array(
			'label'    => __('User','supportcandy'),
			'value'    => '',
			'flag_val' => "user",
			'slug'     => $field_slug,
		);
		
		$output[]   = array(
			'label'    => __('Guest','supportcandy'),
			'value'    => '',
			'flag_val' => "guest",
			'slug'     => $field_slug,
		);
		break;


		
		
				
	default:
		
			$output = apply_filters('wpsc_filter_autocomplete',$output,$term,$field_slug);
			global $current_user, $wpdb;


			if (!$output) {

				$termslug    = 'agent_'.$current_user->ID;
			  $agentsql    = "SELECT * FROM ".$wpdb->prefix."terms WHERE `slug` ='".$termslug."'";
			  $agenterm    = $wpdb->get_results($agentsql);
			  
			
				$get_all_meta_keys = $wpscfunction->get_all_meta_keys();
				
				$sql = "SELECT DISTINCT t.*  FROM ".$wpdb->prefix."wpsc_ticket t ";
				
				$join_str ='';
				
				$join = array();

				$where = "";

				if($_SESSION['user_type'] == 'subscriber'){				

					if(empty($_REQUEST['term'])){
					 $where .= "WHERE t.customer_email = '".$current_user->user_email."' AND t.active = '1' AND t.ticket_status != '3'";
					}
				}elseif($_SESSION['user_type']== 'supplier'){

					if(empty($_REQUEST['term'])){
						$where .= "WHERE t.agent_created = '".$agenterm[0]->term_id."' AND t.active = '1'";
					}
					


				}

				
				
				if(in_array($field_slug,$get_all_meta_keys)){

					if($term){	
					$current_agent_id  = $wpscfunction->get_current_user_agent_id();							
						
						$join[] = $field_slug;
						$alice  = str_replace('-','_',$field_slug);


						

						if($_SESSION['user_type']== 'supplier'){

     			     $where .= " WHERE " .$alice.".meta_value LIKE '$term%' AND t.agent_created = '$current_agent_id'"; ;
     			 
					 	}elseif($_SESSION['user_type']== 'subscriber'){
					 		  $where .= " WHERE " .$alice.".meta_value LIKE '$term%' AND t.customer_email = '$current_user->user_email' AND t.active = '1' AND t.ticket_status != '3'";
					 	}
					
					}
					
				}else {					
				
					if($term){							
						
						$where .= " WHERE t.$field_slug LIKE '$term%' AND t.active = '$active'";
					}
				}
				
				$limit = "  LIMIT " .'10'."  OFFSET " .'0' ;

				foreach ( $join as $slug ) {
					$alice = str_replace('-','_',$slug);
					
					$join_str = "JOIN {$wpdb->prefix}wpsc_ticketmeta ".$alice." ON t.id = ".$alice.".ticket_id AND ".$alice.".meta_key = '".$slug."' ";
                }
		  
				//combining query
              
				  $sql = $sql . $join_str .$where  . $limit;
				 $ticket_data = $wpdb->get_results($sql);
				 			
				// print_r($sql);
				
				$tickets = json_decode(json_encode($ticket_data), true);
	 
				foreach($tickets as $ticket){



					if(in_array($field_slug,$get_all_meta_keys)){
						$result = $wpscfunction->get_ticket_meta($ticket['id'],$field_slug,true);
					}
					else {
						$result = $wpscfunction->get_ticket_fields($ticket['id'],$field_slug);
					}
					if(!in_array($result,$output)){

						$output[] = array(
							'label'    => $result,
							'value'    => '',
							'flag_val' => $result,
							'slug'     => $field_slug,
						);
					}
				}
			}


			
			if($field_slug == 'customer_name'){


				  $users = get_users(array('search'=>'*'.$term.'*'));

				  //print_r($users);



	/*			 $createdbyexplode    = explode(",",$users->created_by);

			 		 print_r($createdbyexplode);*/

			 		 //print_r($agentuserid);


			 	foreach ($users as $user) {	


			 	 if($_SESSION['user_type']== 'supplier'){

     			  $arraycheck          = explode(",",$user->created_by);
     			  $useridcheck         = $current_user->ID;
     			 
				 	}elseif($_SESSION['user_type']== 'subscriber'){
				 		 $arraycheck         =  $agentuserid;
     			   $useridcheck        =  $user->id;
				 	}

			 			if (in_array($useridcheck, $arraycheck))			 			
  						{  							
			 	        $company  = get_user_meta( $user->id, 'company', true );
							if(!empty($company)){
								$output[] = array(
									'label' => $company,
									'value' => '',
									'flag_val' => $company,
									'slug'     => $field_slug,
								);
						  }
						}// in_array
				}
					
			}else if($field_slug == 'customer_email'){
				$users = get_users(array('search'=>'*'.$term.'*','number' => 5));
				foreach ($users as $user) {
					$output[]=array(
						'label'=> $user->user_email,
						'value'=> '',
						'flag_val' => $user->user_email,
						'slug' => $field_slug,
					);
				}
			}else if($field_slug == 'description'){

				if($term){

					  $ticketsid =array();
					  if($_SESSION['user_type'] == 'subscriber'){

						    $ticketdescriptionsql = "SELECT * FROM ".$wpdb->prefix."wpsc_ticket WHERE `customer_email` ='".$current_user->user_email."' AND `active`='1' AND `ticket_status` !='3'";
						}elseif($_SESSION['user_type'] == 'supplier'){

							$current_agent_id  = $wpscfunction->get_current_user_agent_id();

							$ticketdescriptionsql = "SELECT * FROM ".$wpdb->prefix."wpsc_ticket WHERE `agent_created` ='".$current_agent_id."' AND `active`='1'";
						}

						$ticketdatadescription  = $wpdb->get_results($ticketdescriptionsql);


						/*get ticket id form wp_wpsc_ticket*/
						if(isset($ticketdatadescription) && !empty($ticketdatadescription)){
							foreach ($ticketdatadescription as $key => $value) {
								 array_push($ticketsid, $value->id);
							}	
						}//wp_wpsc_ticket


				$sql555 = "SELECT * FROM ".$wpdb->prefix."posts WHERE `post_content` LIKE '%".$term."%' AND `post_type` ='wpsc_ticket_thread'";

        $ticket_datasql555 = $wpdb->get_results($sql555);

          $ticketsmatchid =array();
          if(isset($ticket_datasql555) && !empty($ticket_datasql555)){
          //get ticket id 	
	        foreach ($ticket_datasql555 as $key => $postdata) {

	              $ticket_id = get_post_meta($postdata->ID, 'ticket_id', true);

	              if (in_array($ticket_id, $ticketsid))
								  {
								    
								       array_push($ticketsmatchid, $ticket_id);
								  }

	           } 

	         }//post 		

	         if(isset($ticketsmatchid) && !empty($ticketsmatchid)){
		          //get ticket id 	
			         foreach ($ticketsmatchid as $key => $value) {
			         	   
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
														      'value'   => $value,
														      'compare' => '='
															))
														);
													 $result = get_posts($args);
													 $text =  substr($result[0]->post_content, 0, 100);
													if($text != 'NA')
													{
														$content = strip_tags($text);
													}

										
										if(!in_array($result,$output)){

											$output[] = array(
												'label'    => $content,
												'value'    => '',
												'flag_val' => $value,
												'slug'     => 'description',
											);
										}

			         }// end  of loop
						
					  }

					  
				}      

        
				foreach($tickets as $ticket){	

					

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
								 $result = get_posts($args);
								 $text =  substr($result[0]->post_content, 0, 100);
								if($text != 'NA')
								{
									$content = strip_tags($text);
								}

					
					if(!in_array($result,$output)){

						$output[] = array(
							'label'    => $content,
							'value'    => '',
							'flag_val' => $ticket['id'],
							'slug'     => 'description',
						);
					}
				}

			}
			
			break;
}

if (!$output) {
  $output[] = array(
		'label'    => __('No matching data','supportcandy'),
		'value'    => '',
		'flag_val' => '',
		'slug'     => '',
	);
}

if ($output) {
	$output = array_unique($output,SORT_REGULAR);
}

if (!isset($source)) {
	echo json_encode($output);
}
