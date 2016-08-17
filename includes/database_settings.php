<?php
/**
 * Plugin Activation.
 *
 * @param void
 *
 * @return void
 */
function custom_push_notification_activation()
{
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . 'custom_notifications';
    $sql = "
        CREATE TABLE IF NOT EXISTS `".$table_name."` (
          `cn_id` int(11) NOT NULL AUTO_INCREMENT,
          `post_id` int(11) NOT NULL,
          `post_type` varchar(32) NOT NULL DEFAULT 'events',
          `time_zone` varchar(50) NOT NULL DEFAULT '".SERVER_TIME_ZONE."',
          `message` text NOT NULL,
          `scheduled_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
          `cron_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
          `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
          `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
          `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0 - disabled, 1 - active, 2 - send, 6 - deleted',
            PRIMARY KEY (`cn_id`),
  	    KEY `post_id` (`post_id`)
        ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}


/**
 * Add New Custom Push notification
 *
 * @param array $data
 *
 * @return boolean
 */
function add_custom_push_notification($data = array()) {
    global $wpdb;

    $notificaiton_data = array(
        'post_type'      => 'event',
        'post_id'        => (int)$data['post_id'],
        'message'        => esc_sql($data['message']),
        'scheduled_date' => date(CN_DATE_TIME_FORMAT, strtotime($data['scheduled_date'])),
        'cron_time'      => convertToClientTimeZone($data['scheduled_date'],$data['time_zone'],SERVER_TIME_ZONE),
        'time_zone'      => esc_sql($data['time_zone']),
        'date_created'   => current_time('mysql', 1),
        'date_modified'  => current_time('mysql', 1),
        'status'         => (int)$data['status'],
    );

    $wpdb->insert($wpdb->prefix."custom_notifications", $notificaiton_data);

    return TRUE;
}


/**
 * Add New Custom Push notification
 *
 * @param array $data
 *
 * @return boolean
 */
function edit_custom_push_notification($data = array(),$cn_id = 0) {
    global $wpdb;

    $notificaiton_data = array(
        'post_type'      => 'event',
        'post_id'        => (int)$data['post_id'],
        'message'        => esc_sql($data['message']),
        'scheduled_date' => date(CN_DATE_TIME_FORMAT, strtotime($data['scheduled_date'])),
        'cron_time'      => convertToClientTimeZone($data['scheduled_date'],$data['time_zone'],SERVER_TIME_ZONE),
        'time_zone'      => esc_sql($data['time_zone']),
        'status'         => (int)$data['status'],
    );

    $wpdb->update($wpdb->prefix."custom_notifications", $notificaiton_data,array( 'cn_id' => (int)$cn_id ));
    return TRUE;
}


/**
 * Get ush_notifications
 *
 * @param integer $cn_id
 *
 * @return array
 */
function get_push_notifications($cn_id = 0) {

    global $wpdb;
    if ($cn_id) {
        return $wpdb->get_row( "SELECT cn.* FROM ".$wpdb->prefix."custom_notifications cn WHERE cn.status != ".CN_DELETED_STATUS." AND cn.cn_id = ".(int)$cn_id,ARRAY_A );
    } else {
        return $wpdb->get_results( "
        SELECT cn.*,p.post_title
        FROM ".$wpdb->prefix."custom_notifications cn
        INNER JOIN ".$wpdb->prefix."posts p ON (cn.post_id = p.ID)
        WHERE cn.status != ".CN_DELETED_STATUS."
        ORDER BY cn_id DESC",ARRAY_A);
    }
}



/**
 * Delete Push Notifications
 *
 * @return void
 */
function delete_push_notification($cn_id = 0) {
    global $wpdb;

    $notificaiton_data = array(
        'status'        => CN_DELETED_STATUS,
        'date_modified' => current_time('mysql', 1)
    );

    $wpdb->update($wpdb->prefix."custom_notifications", $notificaiton_data,array( 'cn_id' => (int)$cn_id ));
    return TRUE;
}
