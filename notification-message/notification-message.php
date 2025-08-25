<?php

/**
 * Plugin Name:Notification Message
 * Description: Display different messages on top of the web pages
 * Author: Daniyal
 * Version: 1.0.0
 */

if(!defined('ABSPATH')) {
    exit;
}

define('notification_message_path', plugin_dir_path(__FILE__));

require_once notification_message_path . 'admin/settings-page.php';

class Notification_Message {
    private static $instance = null;
    public function __construct() {
        new Settings_Page();
    }

    public static function get_instance() {
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

add_action('plugin_loaded', ['Notification_Message', 'get_instance']);
