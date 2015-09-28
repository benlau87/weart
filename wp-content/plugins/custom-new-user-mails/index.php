<?php
/*
Plugin Name: Custom New User Email
Description: Changes the copy in the email sent out to new users
*/
 
// Redefine user notification function
if ( !function_exists('wp_new_user_notification') ) {
    function wp_new_user_notification( $user_id, $type = '' ) {
        $user = new WP_User($user_id);
 
        $user_login = stripslashes($user->user_login);
        $user_email = stripslashes($user->user_email);
				
				if($type == 'artist') {
 
					$message  = sprintf(__('New user registration on your blog %s:', 'waa'), get_option('blogname')) . "\r\n\r\n";
					$message .= sprintf(__('Username: %s', 'waa'), $user_login) . "\r\n\r\n";
					$message .= sprintf(__('E-Mail: %s', 'waa'), $user_email) . "\r\n";
					$message .= sprintf(__('User aktivieren: %s', 'waa'), home_url('/') . 'wp-admin/users.php?role=wpau_unapproved' ) . "\r\n";

					wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), get_option('blogname')), $message);
					
				}
    }
} 
?>