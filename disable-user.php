<?php
/*
Plugin Name: Disable User
Plugin URI: http://dev.coziplace.com/premium-wordpress-plugins/disable-user
Description: Sometime you want to prevent some users from logging in without having to permanently delete their account, so that you can re-enable them in the future if needed. This plugin allows you to temporary disable any users.  For enhanced version with the ability to set user expiry date, please visit the <a href="http://dev.coziplace.com/premium-wordpress-plugins/user-expiry">Official</a> page.    
Version: 2.0
Author: Narin Olankijanan
Author URI: http://dev.coziplace.com
License: GPLv2
*/


add_action('login_message','d_login_extra_note');

function d_login_extra_note() {

    $msg ='';
    if ($_GET['code']==1) $msg = "<div id='message' class='error'><p>This User has been disabled.</p></div>";
   
    
    echo $msg;
}





add_action('init', 'd_check_exp');

function d_check_exp() {

$user = wp_get_current_user();
   $disabled = get_user_meta($user->ID, 'disabled', true);
   if ($disabled == 1) {
     wp_logout();
     wp_redirect(home_url().'/wp-login.php?code=1');
   
   }
}

add_action( 'admin_menu', 'd_create_menu');

function d_create_menu() {
   
        add_menu_page( 'Disable', 'Disable User','manage_options', 'disable_options','d_list_page','');
        add_submenu_page( 'disable_options', 'Edit User', 'Edit User', 'manage_options', 'd_edit_user', 'd_edit_page');

}

function d_list_page() {
        if (!current_user_can( 'administrator')) {
             wp_die("Insufficient Previleged!.");
         } else {
	
?>        
        <h2>User Disable</h2>
        <h3>List of Users</h3>

<table class="widefat">
<thead><th>Username</th><th>Disable?</th></thead>
<?php
	global $wpdb;
	$sql = "SELECT ID, user_login FROM $wpdb->users";
        $results = $wpdb->get_results( $sql , ARRAY_A);
foreach ($results as $result) {
		$username = $result['user_login'];
        	$disabled = get_user_meta($result['ID'],'disabled',true);        
                $disabled = ($disabled==1 ? 'Disabled' : 'Enabled') ;      
                echo "<tr><td>$username</td><td>$disabled</td><tr>"; 
        }
?>
</table>
<?php 
}
}

function d_edit_page() {
	
        $msg = "";
   
     	if (isset($_POST['username'])) {	
        global $wpdb;
	$sql = "SELECT ID FROM $wpdb->users WHERE user_login='".sanitize_user($_POST['username'])."'";
        $user_id = $wpdb->get_var( $wpdb->prepare($sql) );
        }
        

      if (!current_user_can( 'administrator')) {
             wp_die("Insufficient Previleged!.");
         } else {
             if ($_POST["submit"] == 'Save Change') {
             if ($_POST["disabled"] == 1) {
              update_user_meta( $user_id, 'disabled', 1 );
             } else {
              delete_user_meta( $user_id, 'disabled');
             }
             $msg = "<div id='message' class='updated fade'><p><strong>Setting Saved.</strong></p></div>";
             }
        }
 
        
    
?>
<h2>Edit User</h2>

<?php echo $msg ; ?>
<form name="myform" action="" method="post">
<table class="form-table"><tr><td>Username: </td>
<td>
<select name="username">
<?php
        global $wpdb;
	$sql = "SELECT user_login FROM $wpdb->users";
        $results = $wpdb->get_results( $sql , ARRAY_A);
foreach ($results as $result) {
?><option value="<?php echo $result['user_login']; ?>"><?php echo $result['user_login']; ?></option><?php	 
        }
?>
</select></td></tr><tr>
<td>
Status :</td><td>  
<input type="radio" value="1" name="disabled"> Disable <input type="radio" value="2" name="disabled"> Enable</td></tr><tr><td>

<input name="submit" type="submit" value="Save Change" class="button-secondary" /></td><td></td></tr>
</form>

<?php
}
/* EOF */