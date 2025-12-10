<?php

class WPCM_Contact_Admin{
    public function init(){
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_init', [$this, 'handle_delete']);
        add_action('admin_init', [$this, 'handle_bulk_delete']);
        add_action('admin_init', [$this, 'handle_shortcode_create']);
        add_action('admin_inti', [$this, 'handle_shortcode_update']);
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

        add_submenu_page(
            'wpcm_messages',
            'Shortcodes',
            'Shortcodes',
            'manage_options',
            'wpcm_shortcodes',
            [$this, 'render_shortcodes_page']
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


    public function handle_delete() {

        if (!isset($_GET['action']) || $_GET['action'] !== 'delete') {
            return;
        }

        if (!isset($_GET['id'])) {
            return;
        }

        if (!isset($_GET['_wpnonce']) ||
            !wp_verify_nonce($_GET['_wpnonce'], 'wpcm_delete_message')) {
            wp_die('Security check failed!');
        }

        $id = intval($_GET['id']);

        require_once WPCM_PATH . 'includes/class-db.php';
        $db = new WPCM_Contact_DB();
        $db->delete_submission($id);

        wp_redirect(admin_url('admin.php?page=wpcm_messages&deleted=1'));
        exit;
    }



    public function handle_bulk_delete() {

        if (!isset($_POST['bulk_delete'])) {
            return;
        }

        if (!isset($_POST['wpcm_bulk_delete_nonce']) ||
            !wp_verify_nonce($_POST['wpcm_bulk_delete_nonce'], 'wpcm_bulk_delete_action')) {
            wp_die('Security check failed!');
        }

        if (!isset($_POST['selected_ids']) || empty($_POST['selected_ids'])) {
            return;
        }

        $ids = array_map('intval', $_POST['selected_ids']);

        require_once WPCM_PATH . 'includes/class-db.php';
        $db = new WPCM_Contact_DB();

        foreach ($ids as $id) {
            $db->delete_submission($id);
        }

        wp_redirect(admin_url('admin.php?page=wpcm_messages&bulk_deleted=1'));
        exit;
    }


    public function render_shortcodes_page(){
        include WPCM_PATH . 'templates/admin-shortcodes.php';
    }



    public function handle_shortcode_create() {

        // STEP 1: Check if submit button pressed
        if (!isset($_POST['create_shortcode'])) {
            return;
        }

        // STEP 2: Security nonce
        if (
            !isset($_POST['wpcm_create_shortcode_nonce']) ||
            !wp_verify_nonce($_POST['wpcm_create_shortcode_nonce'], 'wpcm_create_shortcode_action')
        ) {
            wp_die('Security check failed!');
        }

        // STEP 3: Read form data
        $name    = sanitize_text_field($_POST['sc_name']);
        $fields  = isset($_POST['fields']) ? array_map('sanitize_text_field', $_POST['fields']) : [];
        $success = sanitize_text_field($_POST['success_message']);
        $button  = sanitize_text_field($_POST['button_text']);

        // STEP 4: Save to wp_options
        $shortcodes = get_option('wpcm_custom_shortcodes', []);
        $id = uniqid('wpcm_');

        $shortcodes[$id] = [
            'name'   => $name,
            'fields' => $fields,
            'success' => $success,
            'button' => $button
        ];

        update_option('wpcm_custom_shortcodes', $shortcodes);

        // STEP 5: Redirect with notice
        wp_redirect(admin_url('admin.php?page=wpcm_shortcodes&created=1'));
        exit;
    }


    public function handle_shortcode_update() {

    if (!isset($_POST['update_shortcode'])) {
        return;
    }

    if (
        !isset($_POST['wpcm_update_shortcode_nonce']) ||
        !wp_verify_nonce($_POST['wpcm_update_shortcode_nonce'], 'wpcm_update_shortcode_action')
    ) {
        wp_die('Security check failed!');
    }

    $id      = sanitize_text_field($_POST['sc_id']);
    $name    = sanitize_text_field($_POST['sc_name']);
    $fields  = isset($_POST['fields']) ? array_map('sanitize_text_field', $_POST['fields']) : [];
    $success = sanitize_text_field($_POST['success_message']);
    $button  = sanitize_text_field($_POST['button_text']);

    $shortcodes = get_option('wpcm_custom_shortcodes', []);

    if (!isset($shortcodes[$id])) {
        wp_die('Shortcode not found!');
    }

    // Update
    $shortcodes[$id] = [
        'name'    => $name,
        'fields'  => $fields,
        'success' => $success,
        'button'  => $button
    ];

    update_option('wpcm_custom_shortcodes', $shortcodes);

    wp_redirect(admin_url('admin.php?page=wpcm_shortcodes&updated=1'));
    exit;
}




}


?>