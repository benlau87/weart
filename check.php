<?php
include("wp-config.php");
global $wpdb;
$wp_user_roles = get_option($wpdb->prefix."user_roles") or 
  die ("Die Option '".$wpdb->prefix."user_roles' ist defekt.");
echo("Die Option '".$wpdb->prefix."user_roles' ist scheinbar in Ordnung.");
?>