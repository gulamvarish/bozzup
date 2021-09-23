<?php
/*
Plugin Name: User Registration And Login
Plugin URI: 
Description: User Registration And Login
Author: Gulam Varish
Author URI: 
Version: 1.1.0
*/



class UserRegistration {


	function init() {
		

		register_activation_hook(   __FILE__, array( __CLASS__, 'activate'   ) );
		register_deactivation_hook( __FILE__, array( __CLASS__, 'deactivate' ) );		
		
		
        
        add_action( 'wp_footer', array($this,'my_style'));

        add_action( 'wp_ajax_user_datatables', array($this,'user_datatables' ));
        add_action( 'wp_ajax_nopriv_user_datatables', array($this,'user_datatables' ));
        /*create new user*/
        add_action( 'wp_ajax_createnewuser', array($this,'createnewuser' ));
        add_action( 'wp_ajax_nopriv_createnewuser', array($this,'createnewuser' ));
        

        add_action( 'wp_ajax_createnewsupplier', array($this,'createnewsupplier' ));
        add_action( 'wp_ajax_nopriv_createnewsupplier', array($this,'createnewsupplier' ));

        /*Update user*/
        add_action( 'wp_ajax_update_user_data', array($this,'update_user_data' ));
        add_action( 'wp_ajax_nopriv_update_user_data', array($this,'update_user_data' ));

        /*Delete user Fornt end*/

        add_action( 'wp_ajax_delete_user', array($this,'delete_user' ));

        /*company exit*/
        add_action( 'wp_ajax_exit_company', array($this,'exit_company' ));
        add_action( 'wp_ajax_nopriv_exit_company', array($this,'exit_company' ));
        
        /*company email*/
        add_action( 'wp_ajax_exit_email', array($this,'exit_email' ));
        add_action( 'wp_ajax_nopriv_exit_email', array($this,'exit_email' ));

        /*resend username password*/
        add_action( 'wp_ajax_resend_username_password', array($this,'resend_username_password' ));
        add_action( 'wp_ajax_nopriv_resend_username_password', array($this,'resend_username_password' ));

        

		
        /*Add shortcode */

        add_shortcode( 'registrationform', array($this,'user_listing' ));
        add_shortcode( 'registeruser', array($this,'register_user' ));
        add_shortcode( 'updateruser', array($this,'update_user' ));
        add_shortcode( 'registrationsupplier', array($this,'register_supplier' ));

        

        
	}




	function my_style() {


   // if (!is_admin()) {

    	wp_enqueue_style( 'bootstrap-style', 'https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css' );

         wp_enqueue_style( 'datatables-css', 'https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css');    
         wp_enqueue_style( 'dataTables-responsive-css', 'https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css');    	
    	
/*
    	wp_enqueue_style( 'jquerysctipttop', 'http://www.shieldui.com/shared/components/latest/css/light/all.min.css' );*/

    	
    	  wp_enqueue_style( 'edittable-style', plugin_dir_url( __FILE__ ) . 'css/custom.css' );

          if(basename(get_permalink()) == 'customers'){

         wp_enqueue_script( 'jquery-min1', 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js' );  
         }      

         wp_enqueue_script( 'bootstrap-min.js', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js' );          

          wp_enqueue_script( 'dataTables-js', 'https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js', array() );
         wp_enqueue_script( 'dataTables-responsive-js', 'https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js', array() );                

       wp_localize_script( 'ajax-js', 'ajax_params', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

        wp_enqueue_script( 'custom_pdf', plugin_dir_url( __FILE__ ) . 'js/custom.js' );


                // Localize the script with new data
            $common_array = array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),                
                'ajaxnounce'=>  wp_create_nonce("load_more_posts")
            );
            wp_localize_script( 'custom_pdf', 'common_data', $common_array );

            // Enqueued script with localized data.
            wp_enqueue_script( 'custom_pdf' );


    
    //}
  }


	

// function to create the DB / Options / Defaults					
function activate() {
   	global $wpdb;
  	global $your_db_name;

  	$current_user = get_currentuserinfo(); 
 
 
}
/*Sidebar start*/

/*function left_sidebar(){ 

    $current_user = wp_get_current_user();
    ?>


    <div id="wpsc_sm_filters1" class="col-sm-12 col-md-2 wpsc_sidebar">
        <div class="row m-0">   
        <div class="sidebarlogo">
            <img class="logotopimage"  onclick="sidebar_menu();" src="<?php echo plugin_dir_url('/').'supportcandy/asset/images/Logo1-white.png'; ?>">
            <img class="logobottomimage" src="<?php echo plugin_dir_url('/').'supportcandy/asset/images/logobozzup.png'; ?>">
        </div>
        <hr>             
            <?php 

                
                if($_SESSION['user_type']=='supplier'){
        ?>               
              <a href="/" class="sidebar-link"><img onclick="sidebar_menu();" src = "<?php echo plugin_dir_url('/').'supportcandy/asset/images/icon/dashboard.png'; ?>" alt="Dashboard"/><p class="text">Dashboard</p></a>
              <a href="/customers" class="sidebar-link"><img onclick="sidebar_menu();" src = "<?php echo plugin_dir_url('/').'supportcandy/asset/images/icon/customer.png'; ?>" alt="Customers"/><p class="text">Customers</p></a>
              <a href="#" class="sidebar-link"><img onclick="sidebar_menu();" src = "<?php echo plugin_dir_url('/').'supportcandy/asset/images/icon/setting.png'; ?>" alt="Setting"/><p class="text">Setting</p></a>
               <a href="#" id="helpemail" class="sidebar-link"><img onclick="sidebar_menu();" src = "<?php echo plugin_dir_url('/').'supportcandy/asset/images/icon/help.png'; ?>" alt="help"/><p class="text">Help</p></a>
             
              <?php 
              } 
        $wpsc_support_page_id = get_option('wpsc_support_page_id');
        $support_page_url = get_permalink($wpsc_support_page_id);
        $wpsc_allow_sign_out = get_option('wpsc_sign_out');
        if($wpsc_allow_sign_out){?>
            <button class="btn btn-sm pull-right" type="button" id="wpsc_sign_out" onclick="window.location.href='<?php echo wp_logout_url($support_page_url) ?>'" style="background-color:#FF5733 !important;color:#FFFFFF !important;"><i class="fas fa-sign-out-alt"></i> <?php _e('Log Out','supportcandy')?></button>
        <?php  }?>          
        </div>
        
    </div> 
<?php } */

/*End sidebar*/
function page_access(){

$user = wp_get_current_user();

global $wpdb;
$resultdata = $wpdb->get_results( "SELECT * FROM $wpdb->pmpro_memberships_users WHERE `user_id` = '".$user->ID."' ORDER BY id  DESC");

$id = $resultdata[0]->id;

if($resultdata[0]->cycle_period=='Month'){
  $expire = $resultdata[0]->membership_id == 4 ? '+14 Day' :  '+1 month';    
}elseif($resultdata[0]->cycle_period=='Year'){
   $expire = '+1 year'; 
}elseif($resultdata[0]->cycle_period=='Week'){
  $expire = '+1 week';
}elseif($resultdata[0]->cycle_period=='Day'){
  $expire = '+1 day';
}


$today      = strtotime("today midnight");
$startdate  = strtotime($resultdata[0]->startdate);
$expiredate = date(strtotime($expire, $startdate));

if($today > $expiredate && !empty($expiredate)){
  
  ?>
    <script type="text/javascript">
        window.location.href = "/";
    </script>
    
 <?php
}else{

if ( is_user_logged_in() ) {

            if ( in_array( 'supplier', (array) $user->roles )  ) {

                
            }else{ ?>
    <script type="text/javascript">
        window.location.href = "/";
    </script>
    
 <?php  }
}else{  ?>
    <script type="text/javascript">
        window.location.href = "/";
    </script>
    
 <?php } }
}

function user_listing(){

    

 /*check login*/
$this->page_access();

       
             /* $this->left_sidebar();*/
                  echo '<div class="col-sm-12 col-md-12 right-sidebar">
                        <div class="col-12 text-right">
                        <div class="col-12 text-center" id="message">';

                         if(!empty($_SESSION['status'])){
                            echo '<div class="alert alert-success" role="alert" >'.$_SESSION['status'].'</div>';
                            }

                            if(!empty($_SESSION['error'])){
                            echo '<div class="alert alert-danger" role="alert" >'.$_SESSION['error'].'</div>';
                            }

                       echo '</div>
                        <a class="btn btn-primary createcutomerbtn" href="/create-customer">Add Customer</a></div>
                        
                            <table id="user" class="table table-striped table-bordered dt-responsive" style="width:100%" url="'. home_url().'/get_User";>
                                <thead>
                                    <tr role="row">
                                      <th class="username">Username</th>
                                      <th class="name">Name</th>
                                      <th class="email">Email</th>
                                      <th class="company">Company Name</th>
                                      <th class="register">Register Date</th>
                                      <th class="action">Action</th>
                                      
                                    </tr>
                                 </thead>
                                  <tbody>
                                </tbody>
                        </table>
                    </div>
                  
                 
            ';

            if(!empty($_SESSION['status'])){
                   unset($_SESSION["status"]);      
            }
            if(!empty($_SESSION['error'])){
                             unset($_SESSION["error"]);
             }

}


function user_datatables() {
      global $wpdb;
      $currentuser = wp_get_current_user();
      $currentuser->ID;
     

      //$sql   = "SELECT * FROM wp_users WHERE `created_by` = $user->ID";
      $query = "SELECT * FROM wp_users INNER JOIN wp_usermeta ON wp_users.ID = wp_usermeta.user_id
      WHERE FIND_IN_SET($currentuser->ID, wp_users.created_by) and wp_usermeta.meta_key = 'wp_capabilities' and wp_usermeta.meta_value like '%subscriber%'
      ORDER BY wp_users.user_nicename";

     
      $users = $wpdb->get_results($query);

// Array of WP_User objects.
    $return_json = array();
    foreach ( $users as $user ) {

        $name = get_user_meta( $user->ID, 'first_name' , true ).' '.get_user_meta( $user->ID, 'last_name' , true );
        $company = $company = !empty(get_user_meta( $user->ID, 'company' , true ))?get_user_meta( $user->ID, 'company' , true ):'No Added';

        $row = array(
            'id'            => $user->ID,
            'username'      => $user->user_login,
            'name'          => $name,
            'email'         => $user->user_email,
            'company'       => $company,
            'addeddate'     =>  date('Y-m-d', strtotime($user->user_registered)),
            'currentuserid' => $currentuser->ID,



        );
        $return_json[] = $row;
    }



    echo json_encode(array('data' => $return_json));
    wp_die();
}

function register_user(){
    
 /*check login*/
$this->page_access();
        /*$this->left_sidebar();*/
                  echo '<div class="col-sm-12 col-md-10 right-sidebar">
                    <div class="col-12">
                    <h3>Customer Registration</h3>
                        <div class="col-12 text-center" id="message"></div>                        
                            <form id="newusercreate">

                        <div class="form-group row">
                           
                            <div class="col-sm-12">
                            <label>Email</label>
                              <input type="email" class="form-control getfieldvalue" id="email" text="Enter Email" placeholder="Email" emailexit="0">
                            </div>
                          </div>

                          <div class="form-group row">
                          
                            <div class="col-sm-12">
                            <label>Username</label>
                              <input type="text" class="form-control getfieldvalue" id="username" text="Enter Username" placeholder="Username">
                            </div>
                          </div>
                          <div class="form-group row">
                           
                            <div class="col-sm-12">
                            <label>First Name</label>
                              <input type="text" class="form-control getfieldvalue" id="fname" text="Enter First Name" placeholder="First Name">
                            </div>
                          </div>
                          <div class="form-group row">
                           
                            <div class="col-sm-12">
                            <label>Last Name</label>
                              <input type="text" class="form-control" id="lname" text="Enter Last Name" placeholder="Last Name">
                            </div>
                          </div>
                          
                          <div class="form-group row">
                            <div class="col-sm-12">
                            <label>Company Name</label>
                              <input type="text" class="form-control" id="company" text="Enter Company Name" placeholder="Company Name">
                            </div>
                          </div>
                          <div class="form-group row">
                          
                            <div class="col-sm-12">
                            <label>Password</label>
                             <input type="password" class="form-control" minlength="8" id="password" text="Enter Password" placeholder="Password">
                            </div>
                          </div>
                          <div class="form-group row">
                           
                            <div class="col-sm-12">
                            <label>Confirm Password</label>

                              <input type="password" class="form-control" id="cpassword" text="Enter Confirm Password Name" placeholder="Confirm Password">
                            </div>
                          </div>
                        
                          
                          <div class="form-group row">
                            <div class="col-sm-12">
                              <button type="button" id="register"  class="btn btn-primary">Register</button>
                            </div>
                          </div>
                        </form>
                    </div>                
            </div>';

}

function update_user(){


    
 /*check login*/
$this->page_access();

      global $wpdb;
      $user = wp_get_current_user();
      $user->ID;
     
     
      $edituserid  = $_GET['id'];

      //$sql   = "SELECT * FROM wp_users WHERE `created_by` = $user->ID";
      $query = "SELECT * FROM wp_users INNER JOIN wp_usermeta ON wp_users.ID = wp_usermeta.user_id
      WHERE wp_users.ID =$edituserid  and wp_usermeta.meta_key = 'wp_capabilities' and wp_usermeta.meta_value like '%subscriber%'
      ORDER BY wp_users.user_nicename";

     
      $users = $wpdb->get_results($query);
     /* echo "<pre>";
      print_r($users);
       echo "</pre>";
*/



      /*$this->left_sidebar();*/
                  echo '<div class="col-sm-12 col-md-10 right-sidebar">
                   <div class="col-12 text-center" id="message">';

                         if(!empty($_SESSION['status'])){
                            echo '<div class="alert alert-success" role="alert" >'.$_SESSION['status'].'</div>';
                            }

                            if(!empty($_SESSION['error'])){
                            echo '<div class="alert alert-danger" role="alert" >'.$_SESSION['error'].'</div>';
                            }

                       echo '</div>
                    <div class="col-12">
                    <h3>Edit Profile</h3>
                        <div class="col-12 text-center" id="message"></div>                        
                            <form id="newusercreate">
                          <div class="form-group row">
                          
                            <div class="col-sm-12">
                            <label>Username</label>
                              <input type="text" class="form-control getfieldvalue" id="username" text="Enter Username" placeholder="Username" value="'.$users[0]->user_login.'" readonly>
                            </div>
                          </div>
                          <div class="form-group row">
                           
                            <div class="col-sm-12">
                            <label>First Name</label>
                              <input type="text" class="form-control getfieldvalue" id="fname" text="Enter First Name" placeholder="First Name" value="'.get_user_meta($users[0]->ID, 'first_name', true).'">
                            </div>
                          </div>    
                          <div class="form-group row">
                           
                            <div class="col-sm-12">
                            <label>Last Name</label>
                              <input type="text" class="form-control" id="lname" text="Enter Last Name" placeholder="Last Name" value="'.get_user_meta($users[0]->ID, 'last_name', true).'">
                            </div>
                          </div>
                          <div class="form-group row">
                           
                            <div class="col-sm-12">
                            <label>Email</label>
                              <input type="email" class="form-control" id="email" text="Enter Email" placeholder="Email" value="'.$users[0]->user_email.'">
                            </div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-12">
                             <label>Company Name</label>
                              <input type="text" class="form-control" id="company" text="Enter Company Name" placeholder="Company Name" value="'.get_user_meta($users[0]->ID, 'company', true).'">
                            </div>
                          </div>
                          <div class="form-group row">
                          
                            <div class="col-sm-12">
                            <label>Password</label>
                             <input type="password" class="form-control" minlength="8" id="password" text="Enter Password Name" placeholder="********" >

                               <input type="hidden" class="form-control" minlength="8" id="oldpassword" text="Enter Password Name" placeholder="Password" value="'.$users[0]->user_pass.'" >
                            </div>
                          </div>
                          <div class="form-group row">
                           
                            <div class="col-sm-12">
                            <label>Confirm Password</label>
                              <input type="password" class="form-control" id="cpassword" text="Enter Confirm Password" placeholder="********">
                              <input type="hidden" class="form-control" id="userid" value="'.$users[0]->ID.'">
                            </div>
                          </div>
                        
                          
                          <div class="form-group row">
                            <div class="col-sm-12 ">
                              <button type="button" id="updateuser"  class="btn btn-primary">Update</button>
                            </div>
                          </div>
                        </form>
                    
                </div>  
            </div>';

             if(!empty($_SESSION['status'])){
                   unset($_SESSION["status"]);      
            }
            if(!empty($_SESSION['error'])){
                             unset($_SESSION["error"]);
             }

}


function customer_wp_new_user_notification_email( $wp_new_user_notification_email, $user, $blogname ) { 
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
    $headers .= 'From: Bozzup <support@bozzup.net>'; 
    $wp_new_user_notification_email['headers'] = $headers;    
    $wp_new_user_notification_email['message'] = $message;
 
    return $wp_new_user_notification_email;
}


function createnewuser(){

        global $wpdb;
        $user = wp_get_current_user();

   
        $username =  $_POST['data'][0];
        $fname    =  $_POST['data'][1];
        $lname    =  $_POST['data'][2];
        $email    =  $_POST['data'][3];
        $compay   =  $_POST['data'][4];
        $password =  $_POST['data'][5];

        $username = sanitize_text_field($username);
        $password = $password;
        $email    = sanitize_text_field($email);
        $fname    = sanitize_text_field($fname);
        $lname    = sanitize_text_field($lname);
        $nick     = sanitize_text_field($fname);


        $table_name = $wpdb->prefix . "users";      

        $result = $wpdb->get_results ("SELECT * FROM  $table_name WHERE `user_email` = '".$email."'"); 

       

        if (count($result)>0) {
           
            
            $user_id    = $result[0]->ID;
            $created_by = $result[0]->created_by;
            $displayname= $result[0]->display_name;
            $createdby  =   get_user_meta( $user_id, 'created_by', true);

            //$createdbyexplode    = explode(',', $created_by);
            
            $createdbyexplode    = explode(",",$created_by);

            /*Check current user*/
           
             if($user->ID == $user_id){

                   unset($_SESSION['error']);
                    $_SESSION['error'] = 'You can not add yourself';
                    $status = 0;
                    $message1 = 'You can not add yourself';

              }else{

                if (in_array($user->ID, $createdbyexplode))
                  {
                    unset($_SESSION['status']);
                    $_SESSION['status'] = 'Customer has already added';
                    $status = 1;
                    $message1 = 'Customer has already added';  
                  }
                else
                  {                  


                    $created_by = $created_by !='' ? $created_by.','.$user->ID : $user->ID ;

                    //$createdby  = $createdby !='' ? $createdby.', '.$user->ID : $user->ID ;

                    $user_id_role = new WP_User($user_id);
                    $old_roles = $user_id_role->roles;

                    /*print_r($old_roles);

                    eixt;*/

                    array_push($old_roles,"subscriber");

                    if (count($old_roles)>0) {  //  restore bbPress roles
                            foreach($old_roles as $role) {                                
                                $user_id_role->add_role($role);
                            }        
                    }
                                     
                    

                    $company = get_user_meta($user->ID, 'company', true);

                    $table_name = $wpdb->prefix . "users";
                    $wpdb->query( $wpdb->prepare("UPDATE $table_name  SET created_by = '".$created_by."'  WHERE ID = '".$user_id."'") );

                        if($table_name){
                            update_user_meta( $user_id, 'created_by', $created_by);



                            $_SESSION['status'] = 'Customer has added successfully';
                            $status = 1;
                            $message1 = 'Customer has added successfully'; 

                            $to      = $email;
                            $subject = $company." Added As a Customer";

                            $message  = 'Dear '.$displayname.',<br><br>';
                            $message .= "Welcome to ".$company."<br>";
                            $message .= "You are now customer of ".$company.".<br><br>";
                            $message .= "Best Regards<br>";
                            $message .= "<a href='bozzup.net'>bozzup.net</a><br>";
                            

                            // Always set content-type when sending HTML email
                            $headers = "MIME-Version: 1.0" . "\r\n";
                            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                            // More headers
                            $headers .= 'From: Bozzup <support@bozzup.net>' . "\r\n";
                            wp_mail($to,$subject,$message,$headers);

                        }else{

                            unset($_SESSION['status']);
                            $_SESSION['error'] = $user_id->get_error_message();
                            $status = 0;
                            $message1 = $user_id->get_error_message();
                        }

                    } //end of same user check
                }
            
            


        }else{ 


                $table_name = $wpdb->prefix . "usermeta";

                $companyresult = $wpdb->get_results ("SELECT * FROM  $table_name WHERE `meta_key` = 'company' AND `meta_value` = '".$compay."'");        

                if (count($companyresult)>0) {

                    unset($_SESSION['error']);

                    $_SESSION['status'] = 'This company alredy exits!';

                    $status = 0;
                    $message1 = 'This company alredy exits!';

                    wp_send_json(array('status' => $status, 'message' => $message1));
                   
                    exit();  

                }else{

                $userdata = array(
                    'user_login'    => $username,        
                    'user_pass'     => $password,
                    'user_email'    => $email,
                    'first_name'    => $fname,
                    'last_name'     => $lname,
                    'nickname'      => $nick,

                );

                $user_id = wp_insert_user($userdata);


                // Return
                    if (!is_wp_error($user_id)) {

                        /*Set role*/

                        $user_id_role = new WP_User($user_id);
                                   
                        /*$old_roles = $user_id_role->roles;
                       

                                if (count($old_roles)>0) {
                                 array_push($old_roles,"subscriber");  

                                        foreach($old_roles as $role) {                                
                                            $user_id_role->add_role($role);
                                        }        
                                } else{

                                    $user_id_role->set_role('subscriber');
                                }*/
                                $user_id_role->set_role('subscriber');

                        /*UPDATE created_by*/
                        $table_name = $wpdb->prefix . "users";
                        $wpdb->query( $wpdb->prepare("UPDATE $table_name  SET created_by = '".$user->ID."'  WHERE ID = '".$user_id."'") );
                        /*Add company created_by*/
                        add_user_meta( $user_id, 'company', $compay);

                         

                        $blogname = 'Bozzup';

                        $to = $email;
                        $subject = sprintf( 'Login and Password to manage your mockups', $blogname );
                        $message = "<html>
                                <head>
                                <title>Login details email text:</title>
                                </head>
                                <body>
                                <p>Dear ". $username.", </p>
                                <p>We have set up an account for you on Bozzup</p>
                                <table>
                                <tr><td colspan='2'>Here's your login information:</td></tr>
                                <tr>
                                <th style='padding-left:20px'>Username:</th>
                                <td >".$username."</td>                    
                                </tr>
                                <tr>
                                <th style='padding-left:20px'>Password:</th>
                                <td >".$password."</td>                    
                                </tr>
                                
                                <tr><td colspan='2'>To login to your dashboard and manage your mockups, just follow the link below:</td></tr>
                                <tr>
                                <th align='left'>Login:</th>
                                <td>".home_url()."</td></tr>
                                 <tr><td colspan='2'>If you ever need anything, just let us know.</tr>
                                  <tr><td colspan='2'>You can always email us at Support@bozzup.net</td></tr>
                                 <tr style='margin-top:15px; display: block;'><td colspan='2'> The Bozzup team</td></tr>
                                  <tr><td colspan='2'>Bozzup.net</td></tr>
                                </table> 
                                </body>
                                </html>"; 

                        $headers  = "MIME-Version: 1.0" . "\r\n";
                        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                        $headers .= 'From: Bozzup <support@bozzup.net>';          
                       

                        wp_mail($to, $subject, $message, $headers );

                        unset($_SESSION['error']);

                        $_SESSION['status'] = 'Customer is created and an email with credentials is sent to the customer';

                        $status = 1;
                        $message1 = 'Customer is created and an email with credentials is sent to the customer';
                           

                           
                       

                    }else {
                        unset($_SESSION['status']);
                        $_SESSION['error'] = $user_id->get_error_message();
                        $status = 0;
                        $message1 = $user_id->get_error_message();
                        
                    }
                }

            }

        wp_send_json(array('status' => $status, 'message' => $message1));


        die();
}




/*UPDATE user*/

function update_user_data(){

        global $wpdb;
        session_start();
        $user = wp_get_current_user();



        $username =  $_POST['data'][0];
        $fname    =  $_POST['data'][1];
        $lname    =  $_POST['data'][2];
        $email    =  $_POST['data'][3];
        $compay   =  $_POST['data'][4];
        $password =  $_POST['data'][5];
        $oldpassword =  $_POST['data'][7];
        $userid   =  $_POST['data'][8];




        $username       = sanitize_text_field($username);
        $password22     = $password;
        $oldpassword1   = $oldpassword;
        $email          = sanitize_text_field($email);
        $fname          = sanitize_text_field($fname);
        $lname          = sanitize_text_field($lname);
        $nick           = sanitize_text_field($fname);

         
if($password !=''){

     $userdata = array(
            'ID'            => $userid, 
            'user_pass'     => $password22, 
            'user_email'    => $email,
            'first_name'    => $fname,
            'last_name'     => $lname,
            'nickname'      => $nick,

        );

}else{

     $userdata = array(
            'ID'            => $userid,
            'user_email'    => $email,
            'first_name'    => $fname,
            'last_name'     => $lname,
            'nickname'      => $nick,

        );


}
     
        $user_id = wp_update_user($userdata);

        /*if($password !=''){

           $table_name = $wpdb->prefix . "users";
            $wpdb->query( $wpdb->prepare("UPDATE $table_name  SET user_pass = '".$password22."'  WHERE ID = '".$userid."'") );
        }*/


    // Return
        if (!is_wp_error($userid)) {

            /*Set role*/          

            /*UPDATE created_by*/
            $table_name = $wpdb->prefix . "users";
            $wpdb->query( $wpdb->prepare("UPDATE $table_name  SET user_login = '".$username."'  WHERE ID = '".$userid."'") );
            /*Add company created_by*/
            update_user_meta( $userid, 'company', $compay);

            $_SESSION['status'] = 'Profile has updated successfully';

            wp_send_json(array('status' => 1, 'message' => __('Profile has updated successfully')));

        } else {
            $_SESSION['error'] = $user_id->get_error_message();
            wp_send_json(array('status' => 0, 'message' => __($userid->get_error_message())));
        }


        die();
}



function delete_user(){

    global $wpdb;

    session_start();

     if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
       {
        //You should check nonces and user permissions at this point.
        $user_id       = $_REQUEST['data'];
        $currentuserid = $_REQUEST['currentuserid'];

        /*$table_name = $wpdb->prefix . "users";      

        $result = $wpdb->get_results ("SELECT * FROM  $table_name WHERE `ID` = '".$user_id."'");
       
        $newstr  = str_replace($currentuserid, '', $result[0]->created_by);

        $newstr2 = str_replace(',,', ',', $newstr);

        $newstr3 = trim($newstr2, ',');


        exit;*/
        
        //$userid = wp_delete_user($user_id);

        if (!is_wp_error($userid)) {
            $_SESSION['status'] = 'Customer has deleted successfully';
            wp_send_json(array('status' => 1, 'message' => __('Customer has deleted successfully')));
        } else {
            $_SESSION['error'] = $user_id->get_error_message();
            wp_send_json(array('status' => 0, 'message' => __($user_id->get_error_message())));
        }
      
      }
      die();
}

function exit_company(){

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
            
            wp_send_json(array('status' => 0, 'message' => __('')));
        }
      
      }
      die();
}

function exit_email(){

        global $wpdb;
        session_start();

     if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
       {
       
        $email = $_REQUEST['data'];        

        $table_name = $wpdb->prefix . "users";       

        $result = $wpdb->get_results ("SELECT * FROM  $table_name WHERE `user_email` = '".$email."'");

         
        $username = $result[0]->user_login;
        $fname    = get_user_meta($result[0]->ID, 'first_name', true);
        $lname    = get_user_meta($result[0]->ID, 'last_name', true);
        $company  = get_user_meta($result[0]->ID, 'company', true);

       

        if (count($result)>0) {
           
            wp_send_json(array('status' => 1, 'message' => __('Customer with email address already exists!'), 'username'=>$username, 'fname'=>$fname, 'lname'=>$lname, 'company'=>$company));
        } else {
            
            wp_send_json(array('status' => 0, 'message' => __('')));
        }
      
      }
      die();
}

function resend_username_password(){
        global $wpdb, $current_user;
        session_start();

     if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
       {

            $user_id = $_REQUEST['id'];

             


            if(!empty($user_id)){

                   $newpass = wp_generate_password( 12, false ); 

                    $userdata1['ID'] = $user_id;
                    $userdata1['user_pass'] = $newpass;                
                if(wp_update_user( $userdata1 )){

                    $userdata = get_user_by('id', $user_id); 
                    $email = $userdata->user_email;

                    $to      = $email;
                    $subject = 'Your new password';
                    $sender  = get_user_meta($current_user->ID, 'company', true);

                    $message = "<html>
                    <head>
                    <title>Login details email text:</title>
                    </head>
                    <body>
                    <p>Dear ". $userdata->user_login.", </p>                   
                    <table>
                    <tr><td colspan='2'>Here's your login information:</td></tr>
                    <tr>
                    <th style='padding-left:20px; text-align: left;'>Username:</th>
                    <td >".$userdata->user_login."</td>                    
                    </tr>
                    <tr>
                    <th style='padding-left:20px; text-align: left;'>New Password:</th>
                    <td >".$newpass."</td>                    
                    </tr>                 
                   
                     <tr><td colspan='2'>If you ever need anything, just let us know.</tr>
                      <tr><td colspan='2'>You can always email us at Support@bozzup.net</td></tr>
                     <tr style='margin-top:15px; display: block;'><td colspan='2'> The Bozzup team</td></tr>
                      <tr><td colspan='2'>Bozzup.net</td></tr>
                    </table> 
                    </body>
                    </html>"; 

                    $headers  = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $headers .= 'From: Bozzup <support@bozzup.net>';                    
                    
                    $mail = wp_mail( $to, $subject, $message, $headers );

                    if($mail){   
                            wp_send_json(array('status' => 1, 'message' => __('Email and Password has resent to the customer!')));
                    }else{

                        wp_send_json(array('status' => 0, 'message' => __($user_id->get_error_message())));

                    }

                }else{

                     wp_send_json(array('status' => 0, 'message' => __($user_id->get_error_message())));

                }

            }else{

                    wp_send_json(array('status' => 0, 'message' => __('Email and Password has not resend to the customer')));

            }
           

       }



die();

}


/*Add supplier*/
function createnewsupplier(){

        global $wpdb;
        $user = wp_get_current_user();



        $username =  $_POST['data'][0];
        $fname    =  $_POST['data'][1];
        $lname    =  $_POST['data'][2];
        $email    =  $_POST['data'][3];
        $compay   =  $_POST['data'][4];
        $password =  $_POST['data'][5];
        $supplier =  $_POST['data'][7];

        $username = sanitize_text_field($username);
        $password = $password;
        $email    = sanitize_text_field($email);
        $fname    = sanitize_text_field($fname);
        $lname    = sanitize_text_field($lname);
        $nick     = sanitize_text_field($fname);

        $table_name = $wpdb->prefix . "users";
        $result = $wpdb->get_results ("SELECT * FROM  $table_name WHERE `user_email` = '".$email."'");
        
       
       if(count($result)>0){

           /* $user_id_role = new WP_User($result[0]->ID);
            $user_id_role->roles[0];

            $user_id_role->set_role($supplier);
            $user_id_role->set_role($user_id_role->roles[0]); 

            $_SESSION['status'] = 'Customer registration is successfully';
            wp_send_json(array('status' => 1, 'message' => __('Customer registration is successfully')));*/
            //$role->remove_cap( 'read_private_posts' );

       //echo $result[0]->ID;
               



       }else{      

        $userdata = array(
            'user_login'    => $username,        
            'user_pass'     => $password,
            'user_email'    => $email,
            'first_name'    => $fname,
            'last_name'     => $lname,
            'nickname'      => $nick,

        );

        $user_id = wp_insert_user($userdata);


    // Return
        if (!is_wp_error($user_id)) {

            

            /*Set role*/

            $user_id_role = new WP_User($user_id);
            $user_id_role->set_role($supplier); 

            /*UPDATE created_by*/
            
            $wpdb->query( $wpdb->prepare("UPDATE $table_name  SET created_by = '".$user->ID."'  WHERE ID = '".$user_id."'") );
            /*Add company created_by*/
            add_user_meta( $user_id, 'company', $compay);

            /*For login*/
            $creds = array();
            $creds['user_login']    = $username;
            $creds['user_password'] = $password;       

            $user = wp_signon( $creds, false );

            $userID = $user->ID;

            wp_set_current_user( $userID, $user_login );
            wp_set_auth_cookie( $userID, true, false );
            do_action( 'wp_login', $user_login );

            /*end of login */

            $_SESSION['status'] = 'Customer registration is successfully';
            wp_send_json(array('status' => 1, 'message' => __('Customer registration is successfully')));

        } else {
            $_SESSION['error'] = $user_id->get_error_message();
            wp_send_json(array('status' => 0, 'message' => __($user_id->get_error_message())));
        }
    }

        die();
}

/*Add supplier*/
function register_supplier(){
    

        echo '<div class="container p-0">
            <div class="row">
                    <div class="col-12">
                        <div class="col-12 text-center" id="message"></div>                        
                            <form id="newsuppliercreate">
                         
                          <div class="form-row">
                            <div class="col-sm-6 mb-3">
                              <input type="text" class="form-control getfieldvalue" id="username" text="Enter Username" placeholder="Username">
                            </div>
                         
                           
                            <div class="col-sm-6 mb-3">
                              <input type="text" class="form-control getfieldvalue" id="fname" text="Enter First Name" placeholder="First Name">
                            </div>
                          
                           
                            <div class="col-sm-6 mb-3">
                              <input type="text" class="form-control" id="lname" text="Enter Last Name" placeholder="Last Name">
                            </div>
                          
                           
                            <div class="col-sm-6 mb-3">
                              <input type="email" class="form-control" id="email" text="Enter Email" placeholder="Email">
                            </div>
                          
                            <div class="col-sm-6 mb-3">
                              <input type="text" class="form-control" id="company" text="Enter Company Name" placeholder="Company Name">
                            </div>
                          
                          
                            <div class="col-sm-6 mb-3">
                             <input type="password" class="form-control" minlength="8" id="password" text="Enter Password Name" placeholder="Password">
                            </div>
                        
                           
                            <div class="col-sm-6 mb-3">

                              <input type="password" class="form-control" id="cpassword" text="Enter Confirm Password Name" placeholder="Confirm Password">
                            </div>
                        
                        
                          
                         
                          </div>
                           <div class="form-group row mt-5">
                            <div class="col-sm-12 text-center">
                            <input type="hidden" class="form-control" id="supplier" value="supplier" text="Enter Confirm Password Name" placeholder="Supplier">

                              <button type="button" id="registersupplier"  class="btn btn-primary supplier">Sign Up</button>
                              <p class="signp">Already have an account? <a href="/">Sign in</a></p>
                            </div>
                          </div>
                        </form>
                    </div>
                </div>  
            </div>';

}
	


	function deactivate() {
	

		

	}



	/**/
		
	


	

}

$obj = new UserRegistration();



$obj->init();
