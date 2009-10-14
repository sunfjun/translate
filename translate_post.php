<?php
 

define('WP_ADMIN', true);

require('../../../wp-load.php');
require('../../../wp-admin/includes/admin.php');

@header('Content-Type: text/html; charset=' . get_option('blog_charset'));

wp_reset_vars(array('action'));


if ( !is_user_logged_in() )
	wp_die(__('No Admin'));

switch($action) {
case 'update':
if ( isset($_POST[ 'option_page' ]) ) {
	$option_page = $_POST[ 'option_page' ];
	check_admin_referer( $option_page . '-options' );
}
if ( isset($_POST[ 't_key' ]) ){
	$t_key =  $_POST[ 't_key' ];
  if(!wp_insert_translate($t_key))
    wp_die(__('yes admin'));	
}

$goback = add_query_arg( 'updated', 'true', wp_get_referer() );
wp_redirect( $goback );

// wp_redirect(stripslashes($_SERVER['HTTP_REFERER']) );

break;
default:

break;
}
?>