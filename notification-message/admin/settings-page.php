<?php

//prevent direct access to this file
if(!defined('ABSPATH')) {
    exit;
}

class Settings_Page {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_admin_menu() {
        add_options_page(
            'Notification Message Settings',
            'Notification Settings',
            'manage_options',
            'notification-settings',
            [$this, 'add_menu_page_callback']
        );
    }

    public function register_settings() {
        register_setting('notification_message_settings', 'message_content', ['sanitize_callback' => [$this, 'sanitize_option_callback']]);
        register_setting('notification_message_settings', 'message_type', ['sanitize_callback' => [$this, 'sanitize_option_callback']]);
        register_setting('notification_message_settings', 'message_status', ['sanitize_callback' => [$this, 'sanitize_option_callback']]);

        add_settings_section(
            'notification_settings_section',
            'Notification Message Settings',
            '__return_false',
            'notification-settings'
        );

        add_settings_field(
            'message_content',
            'Enter your message',
            [$this, 'message_content_callback'],
            'notification-settings',
            'notification_settings_section'
        );

        add_settings_field(
            'message_type',
            'Chosse the type of your message',
            [$this, 'message_type_callback'],
            'notification-settings',
            'notification_settings_section'
        );

        add_settings_field(
            'message_status',
            'Display notification message',
            [$this, 'message_status_callback'],
            'notification-settings',
            'notification_settings_section'
        );
    }

    public function sanitize_option_callback($option) {
        //sanitizing fields
        $result = esc_html($option);
        return $result;
    }

    public function message_content_callback() {
        //get current message content or recieve null
        $current_message_content = get_option('message_content') ?? null;
        ?>
            <textarea name="message_content" id="message_content" placeholder="Enter your message"><?= $current_message_content ?></textarea>
        <?php
    }

    public function message_type_callback() {
        //get current message type or recieve null
        $current_message_type = get_option('message_type') ?? null;
        //check current message type in radio-checkbox
        $is_checked = fn ($message_type) => ($message_type == $current_message_type) ? 'checked' : '';
        $message_types = ['success', 'error', 'warning', 'information'];
        foreach($message_types as $message_type) {
            ?>
                <label><input type="radio" name="message_type" id="message_type" value="<?= $message_type ?>" <?= $is_checked($message_type) ?>><?= ucfirst($message_type) ?></label></br>
            <?php
        }
    }

    public function message_status_callback() {
        //get current message status or recieve null
        $current_message_status = get_option('message_status') ?? null;
        //check input if status is set true
        $is_checked = $current_message_status ? 'checked' : '';
        ?>
            <label><input type="checkbox" name="message_status" id="message_status" <?= $is_checked ?>>Display notification</label>
        <?php
    }

    public function add_menu_page_callback() {
        ?>
            <form action="options.php" method="post">
                <?= settings_fields('notification_message_settings') ?>
                <?php do_settings_sections('notification-settings'); ?>
                <?php apply_filters('sanitize_text_field', 'notification_message_settings') ?>
                <?php submit_button('Create notification message'); ?>
            </form>
        <?php
    }
}