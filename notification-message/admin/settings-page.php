<?php

if(!defined('ABSPATH')) {
    exit;
}

class Settings_Page {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_admin_menu() {
        add_menu_page(
            'Notification Message Settings',
            'Notification Settings',
            'manage_options',
            'notification-settings',
            [$this, 'add_menu_page_callback']
        );
    }

    public function register_settings() {
        register_setting('notification_message_settings', 'message_content');
        register_setting('notification_message_settings', 'message_type');

        add_settings_section(
            'notification_settings_section',
            'Notification Content & Type Settings',
            null,
            'notification-settings'
        );

        add_settings_section(
            'notification_settings_section2',
            'Notification Content & Type Settings',
            null,
            'notification-settings'
        );

        add_settings_field(
            'message_content',
            'Write your message',
            [$this, 'message_content_callback'],
            'notification-settings',
            'notification_settings_section'
        );

        add_settings_field(
            'message_type',
            'Chosse the type of your message',
            [$this, 'message_type_callback'],
            'notification-settings',
            'notification_settings_section2'
        );
    }

    public function message_content_callback() {
        $message_content = get_option('message_content') ?? null;
        ?>
            <input type="text" name="message_content" id="message_content" value="<?= $message_content ?>" placeholder="Enter your message">
        <?php
    }

    public function message_type_callback() {
        $choosen_message_type = get_option('message_type');
        $checked_radio = fn($message_type) => ($message_type == $choosen_message_type) ? 'checked' : '';
        $message_types = ['success', 'error', 'warning', 'information'];
        foreach($message_types as $message_type) {
            ?>
                <label><input type="radio" name="message_type" id="message_type" value="<?= $message_type ?>" <?= $checked_radio($message_type) ?>><?= ucfirst($message_type) ?></label>
            <?php
        }
    }

    public function add_menu_page_callback() {
        ?>
            <form action="options.php" method="post">
                <?= settings_fields('notification_message_settings') ?>
                <?php do_settings_sections('notification-settings'); ?>
                <?php submit_button('Create notification message'); ?>
            </form>
        <?php
    }
}