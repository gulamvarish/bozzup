<?php
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
    $parenthandle = 'parent-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.
    $theme = wp_get_theme();
    wp_enqueue_style( $parenthandle, get_template_directory_uri() . '/style.css', 
        array(),  // if the parent theme code has a dependency, copy it to here
        $theme->parent()->get('Version')
    );

    wp_enqueue_style( 'twitter-bootstrap-css', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css');
    wp_enqueue_style( 'datatables-css', 'https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css');    
    wp_enqueue_style( 'dataTables-responsive-css', 'https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css');

    wp_enqueue_style( 'child-style', get_stylesheet_uri(),
        array( $parenthandle ),
        $theme->get('Version') // this only works if you have Version in the style header
    );

}
/*Added By Gulam */
add_filter( 'wp_new_user_notification_email', 'custom_wp_new_user_notification_email', 10, 3 );
function custom_wp_new_user_notification_email( $wp_new_user_notification_email, $user, $blogname ) { 
    $user_login = stripslashes( $user->user_login );
    $user_login = stripslashes( $user->user_login );
    $user_email = stripslashes( $user->user_email );
    $login_url  = wp_login_url();
    $key        = get_password_reset_key( $user );
    $message = "
                    <html>
                    <head>
                    <title>Login details email text:</title>
                    </head>
                    <body>
                    <p>Dear ". $user_login.", </p>
                    <p>We have set up an account for you on Bozzup</p>
                    <table>
                    <tr><td colspan='2'>Here's your login information:</td></tr>
                    <tr>
                    <th style='padding-left:20px'>Username:</th>
                    <td >".$user_login."</td>                    
                    </tr>
                    <tr>
                    <th style='padding-left:20px'>Password:</th>
                    <td >".$_SESSION['subscriber_pass']."</td>                    
                    </tr>
                    <tr>
                    <th style='padding-left:20px'>Reset Password:</th>
                    <td>".network_site_url( 'wp-login.php?action=rp&key='.$key.'&login=' . rawurlencode( $user->user_login ), 'login' )."</td>
                    </tr>
                    <tr><td colspan='2'>To login to your dashboard and manage your mockups, just follow the link below:</td></tr>
                    <tr>
                    <th align='left'>Login:</th>
                    <td>".home_url()."</td></tr>
                     <tr><td colspan='2'>If you ever need anything, just let us know.</tr>
                      <tr><td colspan='2'>You can always email us at Support@bozzup.net</td></tr>
                     <tr style='margin-top:15px; display: block;'><td colspan='2'> The Bozzup team</td></tr>
                      <tr><td colspan='2'>Bozzup.net</td></tr>
                    </table> 
                    </body>
                    </html>
                    ";   
 
    $wp_new_user_notification_email['subject'] = sprintf( 'Login and Password to manage your mockups', $blogname );
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: Bozzup <bozzup@bozzup.net>'; 
    $wp_new_user_notification_email['headers'] = $headers;    
    $wp_new_user_notification_email['message'] = $message;
 
    return $wp_new_user_notification_email;
}
/*add_filter( 'login_redirect', 'login_redirect', 10, 3 );
function login_redirect( $url, $query, $user ) {

if(!is_null($user->roles[0])){
        if($user->roles[0] != 'administrator'){
          return home_url();
        } 
    } else{

    } 
}*/

function ace_block_wp_admin() {
    if ( is_admin() && ! current_user_can( 'administrator' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
        wp_safe_redirect( home_url() );
        exit;
    }
}
add_action( 'admin_init', 'ace_block_wp_admin' );

// COMMENTED BY RAHUL AS CUSTOM FIELD COMPANY NAME ADDED
// add_filter( 'gettext', 'user_nickname', 10, 2 );
function user_nickname( $translation, $original )
{  
   if ( 'Nickname' == $original ) {
        return 'Company Name';}  
    return $translation;
}
/*Added By Gulam */



add_action('init', 'myStartSession', 1);
add_action('wp_logout', 'myEndSession');
add_action('wp_login', 'myEndSession');

function myStartSession() {
    if(!session_id()) {
        session_start();       
        $user = wp_get_current_user();
        //$_COOKIE['expire_current_user_type'];
       
        if (isset($_COOKIE['current_user_type']) && !empty($_COOKIE['current_user_type']) ) {
            //echo 'tttttttt'.$_SESSION['user_type'];
             $_SESSION['user_type']= $_COOKIE['current_user_type'];
             
             if(isset($_COOKIE['expire_current_user_type']) && $_COOKIE['current_user_type'] =='supplier'){

                //$expire = 'expire_'.$_COOKIE['expire_current_user_type'];
                 $_SESSION['expire']= $_COOKIE['expire_current_user_type'];
             }

             /*for renew*/
                global $wpdb;
                $resultdata = $wpdb->get_results( "SELECT * FROM $wpdb->pmpro_memberships_users WHERE `user_id` = '".$user->ID."' ORDER BY id  DESC");
                $id = $resultdata[0]->id;
               

                if(isset($resultdata[0]) && $resultdata[0]->cycle_period=='Month'){
                  $expire = $resultdata[0]->membership_id == 4 ? '+14 Day' :  '+1 month';    
                }elseif(isset($resultdata[0]) && $resultdata[0]->cycle_period==''){
                  
                   $expire = '+14 Day'; 
                }

                $today      = strtotime("today midnight");
                $startdate  = strtotime($resultdata[0]->startdate);
                $expiredate = date(strtotime($expire, $startdate));
                if($today > $expiredate && !empty($expiredate)){
                    $_SESSION['expire']= 'account-expire';
                }else{
                    
                     $_SESSION['expire']= 'account-expire1';
                }

                /*for renew*/
             
           
        }

    }
}

function myEndSession() {

    if (isset($_COOKIE['current_user_type'])) {
        unset($_COOKIE['current_user_type']);
      setcookie('current_user_type', '', time() - 3600, '/'); // empty value and old timestamp
   }

   
    session_destroy ();
}

/*by rkumar 6 may 2021*/
function custom_user_profile_fields($user){
    $previous_value = '';
    if( is_object($user) && isset($user->ID) ) {
        $previous_value = get_user_meta( $user->ID, 'company', true );
    }
    if( is_object($user) && isset($user->ID) ) {
         $bvatno = get_user_meta($user->ID, 'pmpro_bvatno', true);
                       

    }

    ?>

    <table class="form-table">
        <tr>
            <th><label for="company">Company Name</label></th>
            <td>
                <input type="text" class="regular-text" name="company" value="<?php echo esc_attr( $previous_value ); ?>" id="company" /><br />
                <span class="description"></span>
            </td>
        </tr>
         <tr>
            <th><label for="bvatno">VAT NO.</label></th>
            <td>
                <input type="text" class="regular-text" name="bvatno" value="<?php echo esc_attr( $bvatno ); ?>" id="bvatno" /><br />
                <span class="description"></span>
            </td>
        </tr>
    </table>
<?php
}
add_action( 'show_user_profile', 'custom_user_profile_fields' );
add_action( 'edit_user_profile', 'custom_user_profile_fields' );
add_action( "user_new_form", "custom_user_profile_fields" );

function save_custom_user_profile_fields($user_id){

   
    $_SESSION['subscriber_pass'] =  $_POST['pass1'];
   //  echo "<pre>"; print_r($_SESSION['subscriber_pass']); die; exit;
    
    if(!current_user_can('manage_options'))
        return false;
    
    //add_action( 'user_profile_update_errors', 'crf_user_profile_update_errors', 10, 3 );

    # save my custom field
    if( isset($_POST['company']) ) {
        update_user_meta( $user_id, 'company', sanitize_text_field( $_POST['company'] ) );
    } else {
        //Delete the company field if $_POST['company'] is not set
        delete_user_meta( $user_id, 'company', $meta_value );
    }

    if( isset($_POST['bvatno']) ) {
        update_user_meta( $user_id, 'pmpro_bvatno', sanitize_text_field( $_POST['bvatno'] ) );
    } else {
        //Delete the company field if $_POST['company'] is not set
        delete_user_meta( $user_id, 'pmpro_bvatno', $meta_value );
    }
}
add_action('user_register', 'save_custom_user_profile_fields');
add_action( 'personal_options_update', 'save_custom_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_custom_user_profile_fields' );


/*Validate company name*/
add_action( 'user_profile_update_errors', 'crf_user_profile_update_errors', 10, 3 );
function crf_user_profile_update_errors( $errors, $update, $user ) {
    if ( $update ) {
        return;
    }

    if ( empty( $_POST['company'] ) ) {
        $errors->add( 'company_error', __( '<strong>ERROR</strong>: Please enter company name.', 'crf' ) );
    }

    
}




/*js add in footer*/

add_action( 'wp_footer', 'my_footer_scripts' );
function my_footer_scripts(){
 
 

    wp_localize_script( 'ajax-js', 'ajax_params', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

       wp_enqueue_script( 'custom-js', get_stylesheet_directory_uri() . '/js/custom.js', array() );



                // Localize the script with new data
            $custom_array = array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),                
                'ajaxnounce'=>  wp_create_nonce("load_more_posts")
            );
            wp_localize_script( 'custom-js', 'custom_data', $custom_array );

            // Enqueued script with localized data.
            wp_enqueue_script( 'custom-js' );

                // Localize the script with new data
          
           

}


/*For User data get*/

add_action( 'wp_ajax_getpostsfordatatables', 'my_ajax_getpostsfordatatables' );
add_action( 'wp_ajax_nopriv_getpostsfordatatables', 'my_ajax_getpostsfordatatables' );

function my_ajax_getpostsfordatatables() {
    global $wpdb;
      $user = wp_get_current_user();
      $user->ID;
      $rorle = $user->roles[0];

      //$sql   = "SELECT * FROM wp_users WHERE `created_by` = $user->ID";
      $query = "SELECT * FROM wp_users INNER JOIN wp_usermeta ON wp_users.ID = wp_usermeta.user_id
      WHERE wp_users.created_by = $user->ID and wp_usermeta.meta_key = 'wp_capabilities' and wp_usermeta.meta_value like '%subscriber%'
      ORDER BY wp_users.user_nicename";

     
      $users = $wpdb->get_results($query);

// Array of WP_User objects.
    $return_json = array();
    foreach ( $users as $user ) {

        $name = get_user_meta( $user->ID, 'first_name' , true ).''.get_user_meta( $user->ID, 'last_name' , true );
        $company = $company = !empty(get_user_meta( $user->ID, 'company' , true ))?get_user_meta( $user->ID, 'company' , true ):'No Added';

        $row = array(
            'id' => $user->ID,
            'username'     => $user->user_email,
            'name'         => $name,
            'email'        => $user->user_email,
            'company'      => $company,
            'addeddate'    =>  date('Y-m-d', strtotime($user->user_registered))


        );
        $return_json[] = $row;
    }



    echo json_encode(array('data' => $return_json));
    wp_die();
}


/*For Plan*/


if ( ! function_exists('plan') ) {

// Register Custom Post Type
function plan() {

    $labels = array(
        'name'                  => _x( 'Plans', 'Post Type General Name', 'bozzup' ),
        'singular_name'         => _x( 'Plan', 'Post Type Singular Name', 'bozzup' ),
        'menu_name'             => __( 'Plan', 'bozzup' ),
        'name_admin_bar'        => __( 'Post Type', 'bozzup' ),
        'archives'              => __( 'Item Archives', 'bozzup' ),
        'attributes'            => __( 'Item Attributes', 'bozzup' ),
        'parent_item_colon'     => __( 'Parent Item:', 'bozzup' ),
        'all_items'             => __( 'All Items', 'bozzup' ),
        'add_new_item'          => __( 'Add New Item', 'bozzup' ),
        'add_new'               => __( 'Add New', 'bozzup' ),
        'new_item'              => __( 'New Item', 'bozzup' ),
        'edit_item'             => __( 'Edit Item', 'bozzup' ),
        'update_item'           => __( 'Update Item', 'bozzup' ),
        'view_item'             => __( 'View Item', 'bozzup' ),
        'view_items'            => __( 'View Items', 'bozzup' ),
        'search_items'          => __( 'Search Item', 'bozzup' ),
        'not_found'             => __( 'Not found', 'bozzup' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'bozzup' ),
        'featured_image'        => __( 'Featured Image', 'bozzup' ),
        'set_featured_image'    => __( 'Set featured image', 'bozzup' ),
        'remove_featured_image' => __( 'Remove featured image', 'bozzup' ),
        'use_featured_image'    => __( 'Use as featured image', 'bozzup' ),
        'insert_into_item'      => __( 'Insert into item', 'bozzup' ),
        'uploaded_to_this_item' => __( 'Uploaded to this item', 'bozzup' ),
        'items_list'            => __( 'Items list', 'bozzup' ),
        'items_list_navigation' => __( 'Items list navigation', 'bozzup' ),
        'filter_items_list'     => __( 'Filter items list', 'bozzup' ),
    );
    $args = array(
        'label'                 => __( 'Plan', 'bozzup' ),
        'description'           => __( 'Plan', 'bozzup' ),
        'labels'                => $labels,
        'supports'              => array( 'title' ),
        'hierarchical'          => true,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 20,
        'menu_icon'             => 'dashicons-edit-page',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
    );
    register_post_type( 'plan', $args );

}
add_action( 'init', 'plan', 0 );

}




/*function my_login_redirect( $redirect_to, $request, $user ) {   

    //is there a user to check?
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {

        session_start();
        //check for admins
        if ( in_array( 'administrator', $user->roles ) ) {            
            return $redirect_to;
        }else {

            if (in_array( 'supplier', $user->roles )) {

                 $_SESSION['user_type'] = 'supplier';
            
            }else{            

               $_SESSION['user_type']='subscriber';                
            }

         
            return home_url();
        }
    } else {
        return $redirect_to;
    }
}
 
add_filter( 'login_redirect', 'my_login_redirect', 10, 3 );*/




/*Send Help Email*/

add_action( 'wp_ajax_help_email', 'help_email');
//add_action( 'wp_ajax_nopriv_exit_company', array($this,'exit_company' ));

function help_email(){    
    

     if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
       { 

        global $wpdb;        
        $user = wp_get_current_user(); 

         if(!empty($user->display_name)){

           $name =  $user->display_name;

         }else{
            $name =  $user->user_email;
         }

       

        //$name     =  $_POST['data'][0];       
        $email    =  $_POST['data'][1];
        $role     =  $_POST['data'][2];
        $compay   =  $_POST['data'][3];
        $message1 =  $_POST['data'][4];

        $to = "support@bozzup.net";
        $subject = "Need Help For ".$role;

        $message = "
        <html>
        <head>
        <title>Need Help</title>
        </head>
        <body>
        <p><strong>Hi Admin,</strong></p><br>        
        <table>
                    <tr><td colspan='2'>Here's ".$role." information:</td></tr>
                    <tr>
                    <th style='padding-left:20px; text-align:left;'>Name:</th>
                    <td >".$name."</td>                    
                    </tr>
                    <tr>
                    <th style='padding-left:20px; text-align:left;'>Email:</th>
                    <td >".$email."</td>                    
                    </tr>
                    <tr>
                    <th style='padding-left:20px; text-align:left;'>Compay:</th>
                    <td >".$compay."</td>                    
                    </tr>
                    <tr>
                    <th style='padding-left:20px; text-align:left;'>Message:</th>
                    <td >".$message1."</td>                    
                    </tr>
                   
                    </table> 
        <p style='text-transform: capitalize;'><strong>Best Regards</strong><br>".$name."</p> 
       
        </body>
        </html>
        ";

       // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
     // More headers
        $headers .= 'From: <'.$user->user_email.'>' . "\r\n";

        
        

        if (wp_mail($to, $subject, $message, $headers)) {
           
            wp_send_json(array('success' => 1, 'message' => __('Email has send successfully. I’ll get you an answer back shortly.')));
        } else {
            
            wp_send_json(array('success' => 0, 'message' => __('Some thing went wrong!')));
        }
      
      }
      die();
}

?>


<?php
/*For sesssion expire*/

               
     /*$user = wp_get_current_user();  


     if (isset($user->roles) && isset($_SESSION['user_type']) && empty($_SESSION['user_type'])) {

        session_start();


        //check for admins
        if ( in_array( 'administrator', $user->roles ) ) {         
           }else {
            if ( in_array( 'supplier', $user->roles ) ) {
               echo $_SESSION['user_type'] = 'supplier';

               exit;
            }else if ( in_array( 'subscriber', $user->roles ) ) {
               echo $_SESSION['user_type']='subscriber'; 
              exit;
            }         
        }
    }else{         

        session_start();
         unset ($_SESSION["user_type"]);
        
    } */



/*Remove footer content*/
function remove_footer_admin () 
{
   
}
 
add_filter('admin_footer_text', 'remove_footer_admin');

function change_footer_version() {
    return ' ';
}
add_filter( 'update_footer', 'change_footer_version', 9999 );


/*company exit*/
        
add_action( 'wp_ajax_exit_company1', 'exit_company1');
add_action( 'wp_ajax_nopriv_exit_company1', 'exit_company1');


function exit_company1(){

    global $wpdb;
    session_start();

     if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
       {
       
         $company = $_REQUEST['data'];

        

        $table_name = $wpdb->prefix . "usermeta";

        $result = $wpdb->get_results ("SELECT * FROM  $table_name WHERE `meta_key` = 'company' AND `meta_value` = '".$company."'");

            

        if (count($result)>0) {
           
            wp_send_json(array('status' => 1, 'message' => __('This company alredy exits!')));
        } else {
            
            wp_send_json(array('status' => 0, 'message' => __(" ")));
        }
      
      }
      die();
}


function add_additional_class_on_li($classes, $item, $args) {
    if(isset($args->add_li_class)) {
        $classes[] = $args->add_li_class;
    }
    return $classes;
}
add_filter('nav_menu_css_class', 'add_additional_class_on_li', 1, 3);
                /* if(isset($_SESSION['user_type'])) {}else{

                    wp_logout();

                    wp_safe_redirect( home_url() );
                    exit();
                   
                 }*/


/*testing expire*/
/*global $wpdb;

    //clean up errors in the memberships_users table that could cause problems
    pmpro_cleanup_memberships_users_table();

    $today = date("Y-m-d", current_time("timestamp"));

    //look for memberships that expired before today
    $sqlQuery = "SELECT mu.user_id, mu.membership_id, mu.startdate, mu.enddate FROM $wpdb->pmpro_memberships_users mu WHERE mu.status = 'active' AND mu.enddate IS NOT NULL AND mu.enddate <> '0000-00-00 00:00:00' AND DATE(mu.enddate) <= '" . esc_sql( $today ) . "' ORDER BY mu.enddate";

    if(defined('PMPRO_CRON_LIMIT'))
        $sqlQuery .= " LIMIT " . PMPRO_CRON_LIMIT;

    $expired = $wpdb->get_results($sqlQuery);

   print_r($expired);*/



   //add_action("pmpro_cron_expire_memberships", "pmpro_cron_expire_memberships");
function pmpro_cron_expire_memberships1()
{
    global $wpdb;

    //clean up errors in the memberships_users table that could cause problems
    pmpro_cleanup_memberships_users_table();

    $today = date("Y-m-d", current_time("timestamp"));

    //look for memberships that expired before today
     $sqlQuery = "SELECT mu.user_id, mu.membership_id, mu.startdate, mu.enddate FROM $wpdb->pmpro_memberships_users mu WHERE mu.status = 'active' AND mu.enddate IS NOT NULL AND mu.enddate <> '0000-00-00 00:00:00' AND DATE(mu.enddate) <= '" . esc_sql( $today ) . "' ORDER BY mu.enddate";

    if(defined('PMPRO_CRON_LIMIT'))
        $sqlQuery .= " LIMIT " . PMPRO_CRON_LIMIT;

    $expired = $wpdb->get_results($sqlQuery);

   
    
    foreach($expired as $e)
    {


        do_action("pmpro_membership_pre_membership_expiry", $e->user_id, $e->membership_id );

        //remove their membership
        pmpro_changeMembershipLevel(false, $e->user_id, 'expired', $e->membership_id);

        do_action("pmpro_membership_post_membership_expiry", $e->user_id, $e->membership_id );

        $send_email = apply_filters("pmpro_send_expiration_email", true, $e->user_id);


        if($send_email)
        {      
            //send an email
            $pmproemail = new PMProEmail();
            $euser = get_userdata($e->user_id);
            if ( ! empty( $euser ) ) {

                $pmproemail->sendMembershipExpiredEmail($euser);

                if(current_user_can('manage_options')) {
                    printf(__("Membership expired email sent to %s. ", 'paid-memberships-pro' ), $euser->user_email);
                } else {
                    echo ". ";
                }
            }
        }
       
    }
}
      


/*
    Expiration Warning Emails
*/
//add_action("pmpro_cron_expiration_warnings", "pmpro_cron_expiration_warnings1");
function pmpro_cron_expiration_warnings1()
{
    global $wpdb;

    

    //clean up errors in the memberships_users table that could cause problems
    pmpro_cleanup_memberships_users_table();

     $today = date("Y-m-d 00:00:00", current_time("timestamp"));


     $pmpro_email_days_before_expiration = apply_filters("pmpro_email_days_before_expiration", 1);

    // Configure the interval to select records from
    $interval_start = $today;
    $interval_end = date( 'Y-m-d 00:00:00', strtotime( "{$today} +{$pmpro_email_days_before_expiration} days", current_time( 'timestamp' ) ) );

    //look for memberships that are going to expire within one week (but we haven't emailed them within a week)
/*
    echo "SELECT DISTINCT
                mu.user_id,
                mu.membership_id,
                mu.startdate,
                mu.enddate,
                um.meta_value AS notice
            FROM {$wpdb->pmpro_memberships_users} AS mu
              LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id = mu.user_id
                AND um.meta_key = %s
            WHERE ( um.meta_value IS NULL OR DATE_ADD(um.meta_value, INTERVAL %d DAY) < %s )
                AND ( mu.status = 'active' )
                AND ( mu.enddate IS NOT NULL )
                AND ( mu.enddate <> '0000-00-00 00:00:00' )
                AND ( mu.enddate BETWEEN %s AND %s )
                AND ( mu.membership_id <> 0 OR mu.membership_id <> NULL )
            ORDER BY mu.enddate
            ",
        "pmpro_expiration_notice",
        $pmpro_email_days_before_expiration,
        $today,
        $interval_start,
        $interval_end;*/

    $sqlQuery = $wpdb->prepare(
        "SELECT DISTINCT
                mu.user_id,
                mu.membership_id,
                mu.startdate,
                mu.enddate,
                um.meta_value AS notice
            FROM {$wpdb->pmpro_memberships_users} AS mu
              LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id = mu.user_id
                AND um.meta_key = %s
            WHERE ( um.meta_value IS NULL OR DATE_ADD(um.meta_value, INTERVAL %d DAY) < %s )
                AND ( mu.status = 'active' )
                AND ( mu.enddate IS NOT NULL )
                AND ( mu.enddate <> '0000-00-00 00:00:00' )
                AND ( mu.enddate BETWEEN %s AND %s )
                AND ( mu.membership_id <> 0 OR mu.membership_id <> NULL )
            ORDER BY mu.enddate
            ",
        "pmpro_expiration_notice",
        $pmpro_email_days_before_expiration,
        $today,
        $interval_start,
        $interval_end
    );

    if(defined('PMPRO_CRON_LIMIT'))
        $sqlQuery .= " LIMIT " . PMPRO_CRON_LIMIT;

    $expiring_soon = $wpdb->get_results($sqlQuery);


    //print_r($sqlQuery);

    foreach($expiring_soon as $e)
    {
        $send_email = apply_filters("pmpro_send_expiration_warning_email", true, $e->user_id);
        

        if($send_email)
        {
            //send an email
            $pmproemail = new PMProEmail();
            $euser = get_userdata($e->user_id);
            if ( ! empty( $euser ) ) {
                $pmproemail->sendMembershipExpiringEmail($euser);

                if(current_user_can('manage_options')) {
                    printf(__("Membership expiring email sent to %s. ", 'paid-memberships-pro' ), $euser->user_email);
                } else {
                    echo ". ";
                }
            }
        }

        //delete all user meta for this key to prevent duplicate user meta rows
        delete_user_meta($e->user_id, "pmpro_expiration_notice");

        //update user meta so we don't email them again
        update_user_meta($e->user_id, "pmpro_expiration_notice", $today);
    }
}


      
add_filter("retrieve_password_message", "mapp_custom_password_reset", 99, 4);

function mapp_custom_password_reset($message, $key, $user_login, $user_data )    {

    $message = "Someone has requested a password reset for the following account:

" . sprintf(__('%s'), $user_data->user_email) . "

If this was a mistake, just ignore this email and nothing will happen.

To reset your password, visit the following address:

" . network_site_url( "login?action=rp&key=$key&login=" . rawurlencode( $user_login), 'login' );

 
    return $message;

}



/*disable plugin update*/
add_filter('site_transient_update_plugins', '__return_false');


/*set expired 1 Year*/

add_filter('auth_cookie_expiration', 'my_expiration_filter', 99, 3);
function my_expiration_filter($seconds, $user_id, $remember){

//if "remember me" is checked;
if ( $remember ) {
//WP defaults to 2 weeks;
$expiration = 14*24*60*60; //UPDATE HERE;
} else {
//WP defaults to 48 hrs/2 days;
$expiration = 365*24*60*60; //UPDATE HERE;
}

//http://en.wikipedia.org/wiki/Year_2038_problem
if ( PHP_INT_MAX - time() < $expiration ) {
//Fix to a little bit earlier!
$expiration = PHP_INT_MAX - time() - 5;
}

return $expiration;
}




