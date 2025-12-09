<?php

class WPCM_Contact_Admin{
    public function init(){
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function register_settings() {
    // Save settings in wp_options
    register_setting('wpcm_settings_group', 'wpcm_admin_email');
    register_setting('wpcm_settings_group', 'wpcm_enable_email');
    register_setting('wpcm_settings_group', 'wpcm_success_message');
}


    public function register_menu() {
    
        // Main Menu
        add_menu_page(
            'Contact Master',                  // Page title
            'Contact Master',                  // Menu title
            'manage_options',                  // Capability (admin only)
            'wpcm_messages',                   // Menu slug
            [$this, 'render_messages_page'],   // Callback
            'dashicons-email-alt',             // Icon
            26                                 // Position
        );

        // Submenu → All Messages
        add_submenu_page(
            'wpcm_messages',                   // Parent slug
            'All Messages',                    // Page title
            'All Messages',                    // Menu title
            'manage_options',                  // Capability
            'wpcm_messages',                   // Same slug as parent → makes it default
            [$this, 'render_messages_page']    // Callback
        );

        // Submenu → Settings
        add_submenu_page(
            'wpcm_messages',                   // Parent slug
            'Settings',                        // Page title
            'Settings',                        // Menu title
            'manage_options',                  // Capability
            'wpcm_settings',                   // Unique slug
            [$this, 'render_settings_page']    // Callback
        );
    }


   public function render_messages_page(){

        // 1) DB class include
        require_once WPCM_PATH . 'includes/class-db.php';

        // 2) DB class object create
        $db = new WPCM_Contact_DB();

        // 3) Fetch all messages
        $messages = $db->get_all_submissions();

        // 4) Load template and send $messages
        include WPCM_PATH . 'templates/admin-messages.php';
    }


    public function render_settings_page(){
        include WPCM_PATH . 'templates/admin-settings.php';
    }

}


?>