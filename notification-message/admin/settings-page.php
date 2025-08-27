<?php

//prevent direct access to this file
if(!defined('ABSPATH')) {
    exit;
}

class Settings_Page {
    public function __construct() {
        add_action('admin_init', [$this, 'handle_notification_message_form']);
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
        register_setting('notification_message_settings', 'message_content');
        register_setting('notification_message_settings', 'message_type');
        register_setting('notification_message_settings', 'message_status');

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

    public function handle_notification_message_form() {
        //check if the form is submitted and nonce is set
        if(isset($_POST['notification_message_nonce'])) {
            //verify nonce
            if(wp_verify_nonce($_POST['notification_message_nonce'], 'notification_message_nonce_check')) {
                //retrive message args from post request and sanitize them
                $message_content = sanitize_textarea_field($_POST['message_content']);
                $message_type = esc_attr($_POST['message_type']);
                $message_status = esc_attr($_POST['message_status']);

                $errors = [];

                if(strlen($message_content) == 0) {
                    $errors[] = 'Message content cannot be empty';
                }

                if($message_type == '') {
                    $errors[] = 'You must choose the message type';
                }

                if(!empty($errors)) {
                    $redirect_url = add_query_arg(['errors' => $errors], $_SERVER['HTTP_REFERER']);
                    wp_redirect($redirect_url);
                    exit;
                }

                //update message args in database
                update_option('message_content', $message_content);
                update_option('message_type', $message_type);
                update_option('message_status', $message_status);
                //redirect to the settings page
                $success_message = ['success' => 'Changes Saved successfully'];
                $redirect_url = add_query_arg($success_message, $_SERVER['HTTP_REFERER']);
                wp_redirect($redirect_url);
                exit;
            }
        } else {
            return;
        }
    }

    public function message_content_callback() {
        //get current message content or recieve null
        $current_message_content = get_option('message_content') ?? null;
        $current_message_type = get_option('message_type');
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
                <input type="radio" name="message_type" id="message_type-<?= $message_type ?>" value="<?= $message_type ?>" <?= $is_checked($message_type) ?>>
                <label for="message_type-<?= $message_type ?>" class="<?= $message_type ?> label-settings-admin"><?= ucfirst($message_type) ?></label>
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
            <?php if(isset($_GET['success'])) : //check for success message ?>
                <div class="notice notice-success"><?= $_GET['success'] ?></div>
                <?php remove_query_arg('success'); ?>
            <?php elseif(isset($_GET['errors'])) : //check for error messages ?>
                <?php foreach($_GET['errors'] as $error) : ?>
                    <div class="notice notice-error"><?= $error ?></div>
                <?php endforeach; ?>
                <?php remove_query_arg('errors'); ?>
            <?php endif; ?>
            <form method="POST" action="">
                <?php wp_nonce_field('notification_message_nonce_check', 'notification_message_nonce') ?>
                <?= settings_fields('notification_message_settings') ?>
                <?php do_settings_sections('notification-settings'); ?>
                <?php submit_button('Create notification message'); ?>
            </form>
        <?php
    }
}