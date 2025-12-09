<?php  

/**
 * Plugin Name: WP Contact Master
 * Description: simple OOP-based contact form plugin.
 * Version: 1.0
 * Author: MSK
 */

if(!defined('ABSPATH')) exit;

define('WPCM_PATH',plugin_dir_path(__FILE__));
define('WPCM_URL',plugin_dir_url(__FILE__));

// core class load 
require_once WPCM_PATH . 'includes/class-contact-master.php';

function wpcm_init_plugin(){
    $plugin = new WPCM_Contact_Master();
    $plugin->init();
}

add_action('plugins_loaded','wpcm_init_plugin');








?>