<?php

/**
 * Plugin Name:Notification Message
 * Description: Display different messages on top of the web pages
 * Author: Daniyal
 * Version: 1.0.0
 */

//prevent direct access to this file
if(!defined('ABSPATH')) {
    exit;
}

//define constants for plugin path and url
define('notification_message_path', plugin_dir_path(__FILE__));
define('notification_message_url', plugin_dir_url(__FILE__));

require_once notification_message_path . 'admin/settings-page.php';

class Notification_Message {
    private static $instance = null;
    public function __construct() {
        new Settings_Page();
        add_action('wp_head', [$this, 'display_notification_message']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public static function get_instance() {
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function enqueue_assets() {
        //include css file
        wp_enqueue_style(
            'style',
            notification_message_url . 'assets/style.css'
        );
    }

    public function display_notification_message() {
        //retrive notification message content, type, status
        $message_content = get_option('message_content') ?? null;
        $message_type = get_option('message_type') ?? null;
        $message_status = get_option('message_status') ?? false;

        //check notification message status
        if($message_status) {
            //check if thenotification message is not empty
            if(!empty($message_content) && !empty($message_type)) {
                ?>
                    <div class="<?= $message_type ?> plugin-notificaion-div">
                        <p><?= $message_content ?></p>
                    </div>
                <?php
            }
        }
        return;
    }
}

add_action('plugin_loaded', ['Notification_Message', 'get_instance']);
