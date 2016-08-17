<?php
/**
 * Plugin Name: Custom Push Notifications
 * Plugin URI:
 * Description: This plugin save messages and schedule date for custom push notifications
 * Version: 1.0.0
 * Author: Sankar <sankar@mobomo.com>
 * Author URI:
 * License: GPL2
 */

if (!defined('CUSTOM_PUSH_PATH'))
    define('CUSTOM_PUSH_PATH', plugin_dir_path(__FILE__));
if (! defined ( 'LEVICK_SIGNUP' ))
    define ( 'CUSTOM_PUSH_DIR_PATH', plugin_dir_url ( __FILE__ ) );
if (! defined ( 'PUSH_MSG_CHAR_LIMIT' ))
    define ( 'PUSH_MSG_CHAR_LIMIT', 256 );
if (! defined ( 'CN_DATE_TIME_FORMAT' ))
    define ( 'CN_DATE_TIME_FORMAT', 'Y-m-d H:i:s' );
if (! defined ( 'SERVER_TIME_ZONE' ))
    define ( 'SERVER_TIME_ZONE', 'UTC' );
if (! defined ( 'CN_DELETED_STATUS' ))
    define ( 'CN_DELETED_STATUS', '6' );
if (! defined ( 'CN_DISABLED_STATUS' ))
    define ( 'CN_DISABLED_STATUS', '0' );
if (! defined ( 'CN_SEND_STATUS' ))
    define ( 'CN_SEND_STATUS', '2' );
if (! defined ( 'CN_ACTIVE_STATUS' ))
    define ( 'CN_ACTIVE_STATUS', '1' );



include_once('includes/plugin_settings.php');
include_once('includes/database_settings.php');
include_once('includes/handle_messages.php');

function push_notification_admin_styles_scripts( $hook ) {

    wp_enqueue_script( 'jquery-ui-datetimepicker', CUSTOM_PUSH_DIR_PATH . 'assets/js/cpn_scripts.js',  array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'),time(),true );
    wp_enqueue_script( 'jquery-ui-datetimepickerjs1', CUSTOM_PUSH_DIR_PATH . 'assets/bootstrap/js/bootstrap.min.js');
    wp_enqueue_style( 'jquery-ui-datetimepickercss', CUSTOM_PUSH_DIR_PATH . 'assets/css/bootstrap-datetimepicker.min.css');
    wp_enqueue_script( 'jquery-ui-datetimepickerjs2', CUSTOM_PUSH_DIR_PATH . 'assets/js/bootstrap-datetimepicker.min.js');
}


add_action( 'admin_enqueue_scripts', 'push_notification_admin_styles_scripts' );
add_action( 'delete_push_notification', 'delete_push_notification' );

register_activation_hook(__FILE__, 'custom_push_notification_activation');

?>