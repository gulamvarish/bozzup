<?php
/*
    UPDATED FOR THE PURPOSE TO GET ONLY SUBSCRIBERS.
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {exit;}

$term = isset($_REQUEST) && isset($_REQUEST['term']) ? sanitize_text_field($_REQUEST['term']) : '';
if (!$term) {exit;}

$output = array();

$args =  array ( 
    'meta_query' => array(
    'relation' => 'AND',
    array(
    'relation' => 'OR',
    array(
        'key'     => 'first_name',
        'value'   => $term,
        'compare' => 'LIKE'
    ),
    array(
        'key'     => 'last_name',
        'value'   => $term,
        'compare' => 'LIKE'
    ),
    array(
        'key'     => 'company',
        'value'   => $term,
        'compare' => 'LIKE'
    )
   )
  
    )
  );

 



$wp_user_query = new WP_User_Query($args);
$usermeta = $wp_user_query->get_results();


/*
    role added by rkumar.
*/
$users = get_users(array('search'=>'*'.$term.'*','number' => 5, 'role'=>'subscriber'));



$users_dup = array_merge($users,$usermeta);
$users    = array_unique($users_dup, SORT_REGULAR);


 //echo "<pre>"; print_r($users);
foreach ($users as $user) {

$createdbyexplode    = explode(",",$user->created_by);

  if (in_array($current_user->ID, $createdbyexplode))
  {
  
  
    $company = get_user_meta( $user->ID, 'company', true );

    	$output[] = array(
        'id' => $user->ID,
        // 'label' => $user->user_nicename,
        // 'value' => $user->user_nicename,
        'label' => $company,
        'value' => $company,    
        'email' => $user->user_email,
        'created_by' => $current_user->ID,
      );
    }
}

if (!$output) {
  $output[] = array(
    'id' => '',
    'label' => __('No match found','supportcandy'),
    'value' => '',
    'email' => '',
  );
}

echo json_encode($output);
