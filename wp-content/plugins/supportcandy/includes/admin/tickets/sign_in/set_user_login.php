<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Check nonce
if( !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce']) ){
    die(__('Cheating huh?', 'supportcandy'));
}

$username = isset($_POST['username']) ? sanitize_text_field($_POST['username']) : '';
if (!$username) die();

$password = isset($_POST['password']) ? $_POST['password'] : '';
if (!$password) die();

$usertype = isset($_POST['user_type']) ? $_POST['user_type'] : '';

$remember = isset($_POST['remember']) ? true : false;

$wpsc_captcha        = get_option('wpsc_login_captcha',0);
$wpsc_recaptcha_type = get_option('wpsc_recaptcha_type');
$wpsc_get_secret_key = get_option('wpsc_get_secret_key');
if($wpsc_captcha){
	if($wpsc_recaptcha_type){
		$captcha_key =  isset($_COOKIE) && isset($_COOKIE['wpsc_secure_code']) ? intval($_COOKIE['wpsc_secure_code']) : 0;
		if(!isset($_POST['captcha_code']) || !wp_verify_nonce($_POST['captcha_code'],$captcha_key)){
		    die(__('Cheating huh?', 'supportcandy'));
		}
	}
	else {
		if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){
        $secret = $wpsc_get_secret_key;
        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
        $responseData = json_decode($verifyResponse);
        if($responseData->success === false){
					die(__('Cheating huh?', 'supportcandy'));
        }
    }
	}
	setcookie('wpsc_secure_code','123');
}



$creds = array(
  'user_login'    => $username,
  'user_password' => $password,
  'remember'      => $remember,
);
$user = wp_signon( $creds, false );


/*added Bu Gulam*/

/*For get expire user*/
global $wpdb;
$resultdata = $wpdb->get_results( "SELECT * FROM $wpdb->pmpro_memberships_users WHERE `user_id` = '".$user->ID."' ORDER BY id  DESC");



$id = $resultdata[0]->id;
//echo $user->ID;
//print_r($resultdata);




if(isset($resultdata[0]) && $resultdata[0]->cycle_period=='Month'){
  $expire = $resultdata[0]->membership_id == 4 ? '+14 Day' :  '+1 month';    
}elseif(isset($resultdata[0]) && $resultdata[0]->cycle_period==''){
  
   $expire = '+14 Day'; 
}



$today      = strtotime("today midnight");
$startdate  = strtotime($resultdata[0]->startdate);
$expiredate = date(strtotime($expire, $startdate));

if($today > $expiredate && !empty($expiredate)){

    session_start();
    $cookie_name  = "current_user_type";
    $cookie_value = $usertype;
    $_SESSION['user_type'] = $usertype;
    
    

    setcookie($cookie_name, $cookie_value, time() + 31556926, "/"); // 31556926 = 1 Year

    if($usertype == 'supplier'){
      $_SESSION['expire'] = 'account-expire';
      $expire_cookie_name  = "expire_current_user_type";
      $expire_cookie_value = 'account-expire';
      setcookie($expire_cookie_name, $expire_cookie_value, time() + 31556926, "/"); // 31556926 = 1 Year
   }
    
    
     
}else{

if (in_array($usertype, $user->roles))
  {    
    session_start();
    $cookie_name  = "current_user_type";
    $cookie_value = $usertype;
    $_SESSION['user_type'] = $usertype;

    setcookie($cookie_name, $cookie_value, time() + 31556926, "/"); // 31556926 = 1 Year
    
  }else{

     wp_logout(); 

  }


  /*Check expire */

   if($usertype == 'supplier'){
      $_SESSION['expire'] = 'account-not-expire';
   }

}

 if($usertype == 'subscriber'){
    $usertype1 = "customer";
 }else{
    $usertype1 = "supplier";  
    
 }
 
/*added Bu Gulam*/

$response = array();

if (is_wp_error($user)) {  
  $response['error'] = '1';
  $err_codes = $user->get_error_codes();

  if($err_codes[0]=='invalid_username'){
    $response['message'] = "Unknown username. Check again or try your email address.";
  }else if($err_codes[0]=='incorrect_password'){
      $response['message'] = "The password you entered for the user is incorrect. Please try again.";
  }else{
    $response['message'] = $user->get_error_message();
  }
  
}elseif($today > $expiredate && !empty($expiredate)){
  
  if($usertype == 'supplier'){
    $response['error'] = '2';
    $response['message'] = __('Your account is inactive!','supportcandy');
    
  }else{

      $response['error'] = '0';
      $response['message'] = __('Success!','supportcandy');
  }
  
}elseif(!in_array($usertype, $user->roles)){

  $response['error'] = '2';
  $response['message'] = __('Credentials do not match '.$usertype1.' account','supportcandy');

}else {
 


  $response['error'] = '0';
  $response['message'] = __('Success!','supportcandy');
}


echo json_encode( $response );