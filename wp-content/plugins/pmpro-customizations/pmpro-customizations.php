<?php
/*
Plugin Name: PMPro Customizations
Plugin URI: https://www.paidmembershipspro.com/wp/pmpro-customizations/
Description: Customizations for my Paid Memberships Pro Setup
Version: .1
Author: Paid Memberships Pro
Author URI: https://www.paidmembershipspro.com
*/
 
//Now start placing your customization code below this line






//https://bozzup.tecziqnewdemo.com/?pmproeewe_test=1&pmproeewe_test_date=2021-07-26

function custom_pmpro_upcoming_recurring_payment_reminder( $rec_array ) {
     $rec_array = array( 1 => 'membership_recurring', 7 => ' membership_recurring' );
     return $rec_array;
}

add_filter('pmpro_upcoming_recurring_payment_reminder', 'custom_pmpro_upcoming_recurring_payment_reminder', 10, 1);

/*apply_filters( 'pmprorm_send_reminder_to_user', bool $send_mail, WP_User object $euser, MemberOrder $lastorder );*/





/**
 * Only allow users to use the trial level once. This does not affect pre-existing members that had a level before this code is implemented.
 * Be sure to change the $trial_level_id variable in multiple places.
 * You may add this code to your site by following this guide - https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
//record when users gain the trial level
function my_pmpro_after_change_membership_level($level_id, $user_id)
{
     //set this to the id of your trial level
     $trial_levels = array( 4 );
          
     if ( in_array( $level_id, $trial_levels ) ) {     
          //add user meta to record the fact that this user has had this level before
          update_user_meta($user_id, "pmpro_trial_level_used_{$level_id}", "1");
     }    
}
add_action("pmpro_after_change_membership_level", "my_pmpro_after_change_membership_level", 10, 2);
//check at checkout if the user has used the trial level already
function my_pmpro_registration_checks($value)
{
     global $current_user;
     
     $level_id = intval( $_REQUEST['level'] );

     if ( $current_user->ID ) {
          //check if the current user has already used the trial level
          $already = get_user_meta($current_user->ID, "pmpro_trial_level_used_{$level_id}", true);
          
          //yup, don't let them checkout
          if( $already ) {
               global $pmpro_msg, $pmpro_msgt;
               $pmpro_msg = "You have already used up your trial membership. Please select a full membership to checkout.";
               $pmpro_msgt = "pmpro_error";
               
               $value = false;
          }
     }
     
     return $value;
}
add_filter("pmpro_registration_checks", "my_pmpro_registration_checks");

//swap the expiration text if the user has used the trial
function my_pmpro_level_expiration_text($text, $level)
{
     global $current_user;
     
     //has user used trial level already.
     if ( $current_user->ID ) {
          $used_trial = get_user_meta( $current_user->ID, "pmpro_trial_level_used_{$level->id}", true );

          if ( ! empty( $used_trial ) ) {
               $text = "You have already used up your trial membership. Please select a full membership to checkout.";
          }
     }
     
     return $text;
}
add_filter("pmpro_level_expiration_text", "my_pmpro_level_expiration_text", 10, 2);