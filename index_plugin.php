<?php
/**
 * Plugin Name: Raviolino-Booking
 * Description: A booking plugin for WordPress.
 * Version: 1.0.0
 * Author: Ravnized
 * License: GPL3
 */



if(!defined('ABSPATH')) {
    die('You are not allowed to call this page directly.');
}

function log_message($message) {
    if (WP_DEBUG === true) {
        error_log($message);
    }
}
require_once plugin_dir_path(__FILE__) . 'custom_types/mechanicBooking.php';
require_once plugin_dir_path(__FILE__) . 'custom_render/mechanicRendering.php';
require_once plugin_dir_path(__FILE__) . 'shortcode/mechanicShortcode.php';


add_action( 'init', 'register_booking' );

// log message 
log_message('Raviolino-Booking plugin loaded successfully.');





