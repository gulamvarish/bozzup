<?php


$user = wp_get_current_user();
if ( is_user_logged_in() ) {

        if ( in_array( 'supplier', (array) $user->roles ) ) {
                
        }else{
             ?>
    <script type="text/javascript">
        window.location.href = "/";
    </script>
    
 <?php  }
}else{  ?>
    <script type="text/javascript">
        window.location.href = "/";
    </script>
    
 <?php }

	echo pmpro_shortcode_account('');
?>
