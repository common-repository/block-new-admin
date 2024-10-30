<?php 

 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

$dir = plugins_url(); ?>
<h2><?php _e('PLEASE READ CAREFULLY BEFORE ACTIVATION','block-new-admin') ?></h2>
<p><?php _e('This plugin is a security block for creation of new administrators EITHER BY DASHBOARD OR BY CODE. PLUGIN DEACTIVATION OR FOLDER DELETION MANTAIN THE BLOCK ACTIVE. The only way for deactivating the block is to use the password you chose. SO PLEASE PRESERVE YOUR PASSWORD WITH CARE !','block-new-admin') ?><p>
<h2><?php _e('HOW TO USE IT','block-new-admin') ?></h2>
<p><?php _e('Go to Settings / Block New Admin. Into the tab General insert a password and then click on Activate Block. Done! ','block-new-admin'); 
        printf('<strong>%s</strong>', __('BE CAREFULLY: for security reason no messages are shown about your password.', 'block-new-admin'));
?></p>
<h2><?php _e('HOW TO REMOVE BLOCK','block-new-admin') ?></h2>
<p><?php _e('Go to Settings / Block New Admin. Into the tab General insert the password you chose and then click on Deactivate Block. Done! ','block-new-admin'); 
        printf('<br><strong>%s</strong>', __('BE CAREFULLY: for security reason no messages are shown about this operation. If you see Activate Block again your block has been removed', 'block-new-admin'));
?></p>
<h2><?php _e('HOW DOES IT WORK','block-new-admin') ?></h2>
<p><?php _e('When someone tries to create a new administrator, the plugin does not signal any errors.','block-new-admin'); 
        printf('<br><strong>%s</strong>', __('All new users created in this way however have a "HACKER ATTEMPT" role', 'block-new-admin'));
?></p>
<div align="center">
<?php
echo '<img src="' . plugins_url( 'img/no-role.jpg', dirname(__FILE__) ) . '" width="100%" > ';
?>
</div>

