<?php
/**
 * Remove plugin settings data
 *
 * @since 1
 *
 */

//if uninstall not called from WordPress exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
  exit();
}

$option = 'shop_calendar_option_name';
delete_option( $option );