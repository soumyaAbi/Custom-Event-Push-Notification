<?php
/**
 * Messages with the default wordpress classes
 */
if (! defined ( 'MSG_TYPE_SUCCESS' ))
    define ( 'MSG_TYPE_SUCCESS', 'success' );
if (! defined ( 'MSG_TYPE_ERROR' ))
    define ( 'MSG_TYPE_ERROR', 'error' );
if (! defined ( 'MSG_TYPE_NORMAL' ))
    define ( 'MSG_TYPE_NORMAL', 'normal' );


function showMessage($message = '', $msg_type = MSG_TYPE_NORMAL) {
    if (!trim($message)) {
        return FALSE;
    }
    switch ($msg_type) {
        case MSG_TYPE_SUCCESS:
            echo '<div id="message" class="updated notice notice-success is-dismissible">';
            break;
        case MSG_TYPE_ERROR:
            echo '<div id="message" class="error">';
            break;
        case MSG_TYPE_NORMAL:
        default:
            echo '<div id="message" class="updated fade">';
            break;
    }
    echo "<p>$message</p></div>";
}


/**
 * Display custom messages
 */
function show_admin_messages()
{
    if(isset($_COOKIE['wp-admin-messages-normal'])) {
        $messages = strtok($_COOKIE['wp-admin-messages-normal'], "@@");

        while ($messages !== false) {
            showMessage($messages, MSG_TYPE_NORMAL);
            $messages = strtok("@@");
        }

        setcookie('wp-admin-messages-normal', null);
    }

    if(isset($_COOKIE['wp-admin-messages-error'])) {
        $messages = strtok($_COOKIE['wp-admin-messages-error'], "@@");

        while ($messages !== false) {
            showMessage($messages, MSG_TYPE_ERROR);
            $messages = strtok("@@");
        }
        setcookie('wp-admin-messages-error', null);
    }

    if(isset($_COOKIE['wp-admin-messages-success'])) {
        $messages = strtok($_COOKIE['wp-admin-messages-success'], "@@");

        while ($messages !== false) {
            showMessage($messages, MSG_TYPE_SUCCESS);
            $messages = strtok("@@");
        }
        setcookie('wp-admin-messages-success', null);
    }
}


/**
  * Hook into admin notices
  */
add_action('admin_notices', 'show_admin_messages');


/**
 * User Wrapper
 */
function add_admin_message($message = '', $msg_type = MSG_TYPE_NORMAL){

    if(empty($message)) return FALSE;
    switch ($msg_type) {
        case MSG_TYPE_SUCCESS:
            setcookie('wp-admin-messages-success', $_COOKIE['wp-admin-messages-success'] . '@@' . $message, time()+60);
            break;
        case MSG_TYPE_ERROR:
            setcookie('wp-admin-messages-error', $_COOKIE['wp-admin-messages-error'] . '@@' . $message, time()+60);
            break;
        case MSG_TYPE_NORMAL:
            setcookie('wp-admin-messages-normal', $_COOKIE['wp-admin-messages-normal'] . '@@' . $message, time()+60);
        default:
            return FALSE;
    }
}