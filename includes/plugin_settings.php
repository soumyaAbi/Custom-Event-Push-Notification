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

ob_clean();
ob_start();
add_action( 'admin_menu', 'cpn_menu_settings' );



/**
 * Add plugin menu's in admin
 *
 * @return void
 */
function cpn_menu_settings() {

    add_menu_page( 'Custom Push Notifications',
                    'Custom Push Notifications',
                    'administrator',
                    'push_notifications',
                    'push_notifications_list',
                    '',
                     6  );
    add_submenu_page( 'push_notifications', 'Custom Push Notifications', 'Add New', 'administrator', 'push_notification_form', 'push_notification_form' );



}


/**
 * Add New Custom Push Notification
 *
 * @return void
 */
function push_notification_form() {
    global $notification_error, $form_data;
    $event_posts = get_published_events_list();

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && validate_notification_data()) {
        if(isset($_GET['cn_id']) && trim($_GET['cn_id'])) {
            edit_custom_push_notification($_POST,$_GET['cn_id']);
            add_admin_message("You've successfully edited Push Notification data!", MSG_TYPE_SUCCESS);
        } else {
            add_custom_push_notification($_POST);
            add_admin_message("You've successfully added Push Notification data!", MSG_TYPE_SUCCESS);
        }
        wp_redirect(admin_url('/admin.php?page=push_notifications', 'http'), 301);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        set_form_data($_POST);
    } elseif (isset($_GET['cn_id']) && trim($_GET['cn_id'])) {
        $notification_data = get_push_notifications((int)$_GET['cn_id']);
        if ($notification_data) {
            set_form_data($notification_data);
        } else {
            wp_redirect(admin_url('/admin.php?page=push_notifications', 'http'), 301);
            exit;
        }
    }
    include_once CUSTOM_PUSH_PATH . 'templates/form.php';
}


/**
 * Show Push Notifications
 *
 * @return void
 */
function push_notifications_list() {
    if (isset($_GET['action']) && isset($_GET['cn_id'])) {
        delete_push_notification($_GET['cn_id']);
        add_admin_message("You've successfully deleted a Push Notification!", MSG_TYPE_SUCCESS);
        wp_redirect(admin_url('/admin.php?page=push_notifications', 'http'), 301);
        exit;
    } else {
        $notifications_list = get_push_notifications();
        include_once CUSTOM_PUSH_PATH . 'templates/list.php';
    }
}


/**
 * Get Published events list
 *
 * @return array
 */
function get_published_events_list() {
    $args=array(
        'post_type'        => 'event',
        'post_status'      => 'publish',
        'posts_per_page'   => -1,
        'caller_get_posts' => 1
    );

    $my_query = null;
    $my_query = new WP_Query($args);
    if( $my_query->have_posts() ) {
        return $my_query;
    } else {
        return array();
    }
}


/**
 * Validate push notification data
 *
 * @return boolean
 */
function validate_notification_data() {
    global $notification_error;

    if (!(isset($_POST['post_id']) && trim($_POST['post_id']) && is_numeric($_POST['post_id']))) {
        $notification_error['post_id'] = 'Please select an event!';
    }

    if (!(isset($_POST['message']) && trim($_POST['message']))) {
        $notification_error['message'] = 'Please enter a message!';
    } elseif (strlen($_POST['message']) > PUSH_MSG_CHAR_LIMIT){
        $notification_error['message'] = 'Message can have a maximum of '.PUSH_MSG_CHAR_LIMIT.' characters!';
    }

    if (!(isset($_POST['scheduled_date']) && trim($_POST['scheduled_date']))) {
        $notification_error['scheduled_date'] = 'Please select a date!';
    }

    if (!(isset($_POST['time_zone']) && trim($_POST['time_zone']))) {
        $notification_error['time_zone'] = 'Please select a timezone!';
    }

    if ($notification_error) {
        return FALSE;
    } else {
        return TRUE;
    }
}


/**
 * Preload form data
 *
 * @param array $data
 *
 * @return void
 */
function set_form_data($data = array()) {
    global $form_data;

    $form_data['post_id']        = isset($data['post_id']) ? $data['post_id'] : '';
    $form_data['message']        = isset($data['message']) ? stripslashes($data['message']) : '';
    $form_data['scheduled_date'] = isset($data['scheduled_date']) ? $data['scheduled_date'] : '';
    $form_data['time_zone']      = isset($data['time_zone']) ? $data['time_zone'] : 'UTC';
    $form_data['status']         = isset($data['status']) ? $data['status'] : CN_ACTIVE_STATUS;
    $form_data['cron_time']      = isset($data['cron_time']) ? $data['cron_time']: '';
}


/**
 *
 * Convert to client time zone
 *
 * @param string $time_to_convert
 * @param string $time_zone_id_input
 * @param string $time_zone_id_output
 * @param string $return_date_time_format
 *
 * @return string
 */
function convertToClientTimeZone($time_to_convert = '',$time_zone_id_input = SERVER_TIME_ZONE,$time_zone_id_output = SERVER_TIME_ZONE, $return_date_time_format = CN_DATE_TIME_FORMAT){

    if(!trim($time_zone_id_input) || !is_valid_timezone($time_zone_id_input)){
        $time_zone_id_input = SERVER_TIME_ZONE;
    }

    if(!trim($time_zone_id_output) || !is_valid_timezone($time_zone_id_output)){
        $time_zone_id_output = CLIENT_TIME_ZONE;
    }

    $original_datetime = $time_to_convert;
    $original_timezone = new DateTimeZone($time_zone_id_input);

    // Instantiate the DateTime object, setting it's date, time and time zone.
    $datetime = new DateTime($original_datetime, $original_timezone);

    // Set the DateTime object's time zone to convert the time appropriately.
    $target_timezone = new DateTimeZone($time_zone_id_output);
    $datetime->setTimeZone($target_timezone);

    // Outputs a date/time string based on the time zone you've set on the object.
    return $datetime->format($return_date_time_format);
}


/**
 *
 * Check if a time zone is valid
 *
 * @param string $timezoneId
 *
 * @return boolean
 */
function is_valid_timezone($timezoneId = SERVER_TIME_ZONE) {
    try{
        new DateTimeZone($timezoneId);
    }catch(Exception $e){
        return FALSE;
    }
    return TRUE;
}